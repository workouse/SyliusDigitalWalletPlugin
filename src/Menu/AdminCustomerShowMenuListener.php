<?php

declare(strict_types=1);

namespace Workouse\DigitalWalletPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\CustomerShowMenuBuilderEvent;

final class AdminCustomerShowMenuListener
{
    public function addAdminCustomerShowMenuItems(CustomerShowMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $customer = $event->getCustomer();

        if (null !== $customer->getUser()) {
            $menu
                ->addChild('impersonate', [
                    'route' => 'workouse_digital_wallet_credit_index',
                    'routeParameters' => ['customerId' => $customer->getId()],
                ])
                ->setAttribute('type', 'link')
                ->setLabel('workouse_digital_wallet.admin.menu.credits')
                ->setLabelAttribute('icon', 'unhide')
                ->setLabelAttribute('color', 'blue');
        }
    }
}
