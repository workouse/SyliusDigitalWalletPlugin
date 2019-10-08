<?php


namespace Tests\Workouse\DigitalWalletPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class WalletRemovePage extends SymfonyPage
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'workouse_digital_wallet_credit_remove';
    }
}
