<?php

declare(strict_types=1);

namespace Tests\Workouse\SyliusDigitalWalletPlugin\Behat\Page\Shop;

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
