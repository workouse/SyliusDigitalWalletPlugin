<?php


namespace Acme\SyliusExamplePlugin\Service;

use Acme\SyliusExamplePlugin\Entity\Credit;
use Acme\SyliusExamplePlugin\Entity\CreditInterface;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Order\Model\Adjustment;
use Symfony\Component\Security\Core\Security;
use Sylius\Component\Core\Model\OrderInterface;

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

    public function __construct(
        Security $security,
        EntityManager $entityManager,
        CurrencyConverterInterface $currencyConverter,
        CurrencyContextInterface $currencyContext
    )
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->currencyConverter = $currencyConverter;
        $this->currencyContext = $currencyContext;
    }

    public function balance($customer = null)
    {
        return array_sum(array_map(function (Credit $credit) {
            return $this->currencyConverter->convert($credit->getAmount(), $credit->getCurrencyCode(), $this->currencyContext->getCurrencyCode());
        }, $this->entityManager->getRepository(Credit::class)->findBy([
            'customer' => $customer ? $customer : $this->security->getUser()->getCustomer()
        ])));
    }

    public function detractBalance(OrderInterface $order)
    {
        $adjustment = $order->getAdjustments()->filter(function (Adjustment $adjustment) {
            return $adjustment->getType() === 'wallet';
        })->first();

        if (!$adjustment) {
            return;
        }

        $order->removeAdjustment($adjustment);

        $credit = new Credit();
        $credit->setCustomer($order->getUser()->getCustomer());
        $credit->setAmount(-$adjustment->getAmount() > $order->getTotal() ? -$order->getTotal() : $adjustment->getAmount());
        $credit->setAction(CreditInterface::BUY);
        $credit->setCurrencyCode($this->currencyContext->getCurrencyCode());
        $this->entityManager->persist($credit);
        $this->entityManager->flush();
    }

}
