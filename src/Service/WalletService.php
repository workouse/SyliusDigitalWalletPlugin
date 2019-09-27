<?php


namespace Acme\SyliusExamplePlugin\Service;

use Acme\SyliusExamplePlugin\Entity\Credit;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Security\Core\Security;

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

}
