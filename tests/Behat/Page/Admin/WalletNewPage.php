<?php


namespace Tests\Workouse\DigitalWalletPlugin\Behat\Page\Admin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Tests\Workouse\DigitalWalletPlugin\Behat\Behaviour\ContainsErrorTrait;

class WalletNewPage extends SymfonyPage
{
    use ContainsErrorTrait;

    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'workouse_digital_wallet_credit_new';
    }

    public function fillAmount(string $amount): void
    {
        $this->getDocument()->fillField('Amount', $amount);
    }

    public function fillCurrencyCode(string $currencyCode): void
    {
        $this->getDocument()->selectFieldOption('Currency Code', $currencyCode);
    }

    public function fillAction(string $action): void
    {
        $this->getDocument()->fillField('Action', $action);
    }

    public function create(): void
    {
        $this->getDocument()->pressButton('Create');
    }
}
