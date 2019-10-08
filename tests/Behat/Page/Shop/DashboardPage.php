<?php


namespace Tests\Workouse\DigitalWalletPlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Account\DashboardPage as BaseDashboardPage;

class DashboardPage extends BaseDashboardPage
{
    /**
     * {@inheritdoc}
     */
    public function hasCustomerCredit()
    {
        $customerCreditText = $this->getElement('CustomerCredit')->getText();
        return $customerCreditText;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'CustomerCredit' => '#customer-credit',
        ]);
    }
}
