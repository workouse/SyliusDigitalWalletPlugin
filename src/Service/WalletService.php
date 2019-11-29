<?php

declare(strict_types=1);

namespace Workouse\SyliusDigitalWalletPlugin\Service;

use Doctrine\ORM\EntityManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Order\Factory\AdjustmentFactory;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\Order;
use Sylius\Component\Order\Model\OrderItem;
use Symfony\Component\Security\Core\Security;
use Workouse\SyliusDigitalWalletPlugin\Entity\Credit;
use Workouse\SyliusDigitalWalletPlugin\Entity\CreditInterface;

class WalletService
{
    /** @var Security */
    private $security;

    /** @var EntityManager */
    private $entityManager;

    /** @var CurrencyConverterInterface */
    private $currencyConverter;

    /** @var CurrencyContextInterface */
    private $currencyContext;

    /** @var AdjustmentFactory */
    private $adjustmentFactory;

    public function __construct(
        Security $security,
        EntityManager $entityManager,
        CurrencyConverterInterface $currencyConverter,
        CurrencyContextInterface $currencyContext,
        AdjustmentFactory $adjustmentFactory
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->currencyConverter = $currencyConverter;
        $this->currencyContext = $currencyContext;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public function balance($customer = null)
    {
        /** @var ShopUserInterface $user */
        $user = $this->security->getUser();

        return array_sum(array_map(function (Credit $credit) {
            return $this->currencyConverter->convert($credit->getAmount(), $credit->getCurrencyCode(), $this->currencyContext->getCurrencyCode());
        }, $this->entityManager->getRepository(Credit::class)->findBy([
            'customer' => $customer ? $customer : $user->getCustomer(),
        ])));
    }

    public function detractBalance(OrderInterface $order)
    {
        $adjustment = array_sum(array_map(function (OrderItem $orderItem) {
            return array_sum(array_map(function (Adjustment $adjustment) {
                if ($adjustment->getType() === CreditInterface::TYPE) {
                    return $adjustment->getAmount();
                }
            }, $orderItem->getAdjustments()->toArray()));
        }, $order->getItems()->toArray())
        );

        if ($adjustment < 0) {
            /** @var ShopUserInterface $user */
            $user = $order->getUser();

            $credit = new Credit();
            $credit->setCustomer($user->getCustomer());
            $credit->setAmount($adjustment);
            $credit->setAction(CreditInterface::BUY);
            $credit->setCurrencyCode($this->currencyContext->getCurrencyCode());
            $this->entityManager->persist($credit);
            $this->entityManager->flush();
        }
    }

    public function useWallet(Order $order)
    {
        $this->removeWallet($order);

        $adjustmentRate = ($this->balance() * 100) / $order->getTotal();

        if ($adjustmentRate > 100) {
            $adjustmentRate = 100;
        }

        $adjustmentTotal = array_sum(
                array_map(function (OrderItem $orderItem) use ($adjustmentRate) {
                    $adjustment = $this->adjustmentFactory->createNew();
                    $adjustment->setType(CreditInterface::TYPE);
                    $amount = -1 * ($orderItem->getTotal() / 100) * $adjustmentRate;
                    $adjustment->setAmount((int) $amount);
                    $adjustment->setLabel('Wallet');
                    $orderItem->addAdjustment($adjustment);

                    return $adjustment->getAmount();
                }, $order->getItems()->toArray())
            ) * -1;

        $this->entityManager->flush();

        return $adjustmentTotal > 0 ? $adjustmentTotal : 0;
    }

    public function removeWallet(Order $order)
    {
        array_map(function (OrderItem $orderItem) {
            array_map(function (Adjustment $adjustment) use ($orderItem) {
                if ($adjustment->getType() === CreditInterface::TYPE) {
                    $orderItem->removeAdjustment($adjustment);
                }
            }, $orderItem->getAdjustments()->toArray());
        }, $order->getItems()->toArray());

        $this->entityManager->flush();
    }
}
