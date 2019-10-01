<?php


namespace Workouse\DigitalWalletPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CreditInterface extends ResourceInterface
{
    const BUY = "buy";

    public function getCustomer();

    public function setCustomer($customer);

    public function getAmount();

    public function setAmount($amount);

    public function getCurrencyCode();

    public function setCurrencyCode($currencyCode);

    public function getAction();

    public function setAction($action);
}
