<?php


namespace Workouse\DigitalWalletPlugin\Service;

use Doctrine\ORM\EntityManager;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Workouse\DigitalWalletPlugin\Entity\Credit;
use Workouse\ReferralMarketingPlugin\Entity\Reference;
use Workouse\ReferralMarketingPlugin\Service\PromotionInterface;

class PromotionService implements PromotionInterface
{
    /** @var EntityManager */
    private $entityManager;

    /** @var CanonicalizerInterface */
    private $canonicalizer;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    private $referrer;

    private $invitee;

    /** @var FactoryInterface */
    private $customerFactory;

    public function __construct(
        EntityManager $entityManager,
        CanonicalizerInterface $canonicalizer,
        RepositoryInterface $customerRepository,
        $referrer,
        $invitee,
        FactoryInterface $customerFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->canonicalizer = $canonicalizer;
        $this->customerRepository = $customerRepository;
        $this->referrer = $referrer;
        $this->invitee = $invitee;
        $this->customerFactory = $customerFactory;
    }

    function execute(Reference $reference)
    {
        $credit = new Credit();
        $credit->setCustomer($reference->getInvitee());
        $credit->setAction($this->invitee['action']);
        $credit->setAmount($this->invitee['amount']);
        $credit->setCurrencyCode($this->invitee['currency_code']);
        $this->entityManager->persist($credit);

        $customer = $this->customerFactory->createNew();
        $customer->setEmail($reference->getReferrerEmail());
        $customer->setEmailCanonical($this->canonicalizer->canonicalize($reference->getReferrerEmail()));
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $credit = new Credit();
        $credit->setCustomer($this->customerRepository->findOneBy(['id' => $customer->getId()]));
        $credit->setAction($this->referrer['action']);
        $credit->setAmount($this->referrer['amount']);
        $credit->setCurrencyCode($this->referrer['currency_code']);
        $this->entityManager->persist($credit);

        $this->entityManager->flush();
    }
}
