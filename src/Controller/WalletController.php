<?php


namespace Acme\SyliusExamplePlugin\Controller;

use Acme\SyliusExamplePlugin\Entity\Credit;
use Acme\SyliusExamplePlugin\Form\Type\CreditType;
use Proxies\__CG__\Sylius\Component\Core\Model\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return $this->render('@AcmeSyliusExamplePlugin/admin/index.html.twig', [
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

            return $this->redirectToRoute('eres_digital_wallet_credit_index', ['customerId' => $customerId]);
        }

        return $this->render('@AcmeSyliusExamplePlugin/admin/new.html.twig', [
            'form' => $form->createView(),
            'customer' => $customer
        ]);
    }
}
