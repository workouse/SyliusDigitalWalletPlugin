<?php


namespace Workouse\DigitalWalletPlugin\Controller;

use Workouse\DigitalWalletPlugin\Entity\Credit;
use Workouse\DigitalWalletPlugin\Form\Type\CreditType;
use Sylius\Component\Customer\Model\Customer;
use Sylius\Component\Order\Model\Adjustment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class WalletController extends AbstractController
{
    public function indexAction($customerId): Response
    {
        $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy([
            'id' => $customerId
        ]);

        if (!$customer) {
            throw $this->createNotFoundException('Not found user');
        }

        $credits = $this->getDoctrine()->getRepository(Credit::class)->findBy([
            'customer' => $customer
        ]);

        return $this->render('@WorkouseDigitalWalletPlugin/admin/index.html.twig', [
            'credits' => $credits,
            'customer' => $customer
        ]);
    }

    public function newAction($customerId, Request $request): Response
    {

        $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy([
            'id' => $customerId
        ]);

        if (!$customer) {
            throw $this->createNotFoundException('Not found user');
        }

        $credit = new Credit();

        $form = $this->createForm(CreditType::class, $credit);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $credit = $form->getData();
            $credit->setCustomer($customer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($credit);
            $em->flush();

            /** @var FlashBagInterface $flashBag */
            $flashBag = $request->getSession()->getBag('flashes');
            $flashBag->add('success', '');

            return $this->redirectToRoute('workouse_digital_wallet_credit_index', ['customerId' => $customerId]);
        }

        return $this->render('@WorkouseDigitalWalletPlugin/admin/new.html.twig', [
            'form' => $form->createView(),
            'customer' => $customer
        ]);
    }

    public function useAction(Request $request)
    {
        $orderRepository = $this->container->get('sylius.repository.order');
        $orderId = $this->get('sylius.context.cart')->getCart()->getId();
        $order = $orderRepository->findCartById($orderId);
        $walletBalance = $this->get('workouse_digital_wallet.wallet_service')->balance();

        if ($walletBalance > 0) {
            $curretAdjustment = $order->getAdjustments()->filter(function (Adjustment $adjustment) {
                return $adjustment->getType() === "wallet";
            })->first();
            if ($curretAdjustment) {
                $curretAdjustment->setAmount(-$walletBalance);
            } else {
                $discount = new Adjustment();
                $discount->setAmount(-$walletBalance);
                $discount->setType("wallet");
                $order->addAdjustment($discount);
            }
        }

        $this->getDoctrine()->getManager()->flush();

        /** @var FlashBagInterface $flashBag */
        $flashBag = $request->getSession()->getBag('flashes');
        $flashBag->add('success', 'workouse_digital_wallet.balance_used');

        return new RedirectResponse($this->generateUrl('sylius_shop_cart_summary'));

    }

    public function removeAction(Request $request)
    {
        $orderRepository = $this->container->get('sylius.repository.order');
        $orderId = $this->get('sylius.context.cart')->getCart()->getId();
        $order = $orderRepository->findCartById($orderId);

        $curretAdjustment = $order->getAdjustments()->filter(function (Adjustment $adjustment) {
            return $adjustment->getType() === "wallet";
        })->first();

        if ($curretAdjustment) {
            $order->removeAdjustment($curretAdjustment);
            $this->getDoctrine()->getManager()->flush();
        }

        /** @var FlashBagInterface $flashBag */
        $flashBag = $request->getSession()->getBag('flashes');
        $flashBag->add('success', 'workouse_digital_wallet.balance_removed');

        return new RedirectResponse($this->generateUrl('sylius_shop_cart_summary'));
    }
}
