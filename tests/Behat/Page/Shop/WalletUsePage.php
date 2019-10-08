<?php


namespace Tests\Workouse\DigitalWalletPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class WalletUsePage extends SymfonyPage
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'workouse_digital_wallet_credit_use';
    }

    public function getCartTotal()
    {
        $cartTotalText = $this->getElement('cart_total')->getText();
        
        if (strpos($cartTotalText, ',') !== false) {
            return strstr($cartTotalText, ',', true);
        }

        return trim($cartTotalText);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'cart_total' => '#sylius-cart-grand-total',
        ]);
    }
}
