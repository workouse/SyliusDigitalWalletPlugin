<?php


namespace Acme\SyliusExamplePlugin\Menu;

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
                    'route' => 'eres_digital_wallet_credit_index',
                    'routeParameters' => ['customerId' => $customer->getId()]
                ])
                ->setAttribute('type', 'link')
                ->setLabel('eres_digital_wallet.admin.menu.credits')
                ->setLabelAttribute('icon', 'unhide')
                ->setLabelAttribute('color', 'blue');
        }
    }
}
