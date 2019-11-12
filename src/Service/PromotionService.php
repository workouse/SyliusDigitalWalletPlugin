<?php

declare(strict_types=1);

namespace Workouse\DigitalWalletPlugin\Service;

use Doctrine\ORM\EntityManager;
use Sylius\Component\Customer\Model\Customer;
use Workouse\DigitalWalletPlugin\Entity\Credit;
use Workouse\ReferralMarketingPlugin\Entity\Reference;
use Workouse\ReferralMarketingPlugin\Service\PromotionInterface;

class PromotionService implements PromotionInterface
{
    /** @var EntityManager */
    private $entityManager;

    private $referrer;

    private $invitee;

    public function __construct(
        EntityManager $entityManager,
        $referrer,
        $invitee
    ) {
        $this->entityManager = $entityManager;
        $this->referrer = $referrer;
        $this->invitee = $invitee;
    }

    public function referrerExecute(Reference $reference)
    {
        $credit = new Credit();
        $credit->setCustomer($reference->getReferrer());
        $credit->setAction($this->referrer['action']);
        $credit->setAmount($this->referrer['amount']);
        $credit->setCurrencyCode($this->referrer['currency_code']);
        $this->entityManager->persist($credit);
        $this->entityManager->flush();
    }

    public function inviteeExecute(Reference $reference)
    {
        $credit = new Credit();
        $credit->setCustomer($reference->getInvitee());
        $credit->setAction($this->invitee['action']);
        $credit->setAmount($this->invitee['amount']);
        $credit->setCurrencyCode($this->invitee['currency_code']);
        $this->entityManager->persist($credit);
        $this->entityManager->flush();
    }

    public function inviteeUserAfterExecute(Customer $customer)
    {
        $referrer = $this->entityManager->getRepository(Reference::class)->findOneBy([
            'referrer' => $customer,
            'status' => false,
        ]);

        if ($referrer) {
            $credit = new Credit();
            $credit->setCustomer($referrer->getInvitee());
            $credit->setAction($this->invitee['action']);
            $credit->setAmount($this->invitee['amount']);
            $credit->setCurrencyCode($this->invitee['currency_code']);
            $this->entityManager->persist($credit);
            $referrer->setStatus(true);
            $this->entityManager->flush();
        }
    }
}
