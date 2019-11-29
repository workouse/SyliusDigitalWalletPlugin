<?php

declare(strict_types=1);

namespace Tests\Workouse\SyliusDigitalWalletPlugin\Behat\Page\Shop;

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
