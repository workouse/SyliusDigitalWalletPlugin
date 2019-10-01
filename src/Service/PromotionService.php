<?php


namespace Workouse\DigitalWalletPlugin\Service;

use Doctrine\ORM\EntityManager;
use Eres\SyliusReferralMarketingPlugin\Entity\Reference;
use Eres\SyliusReferralMarketingPlugin\Service\PromotionInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Customer\Model\Customer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Workouse\DigitalWalletPlugin\Entity\Credit;

class PromotionService implements PromotionInterface
{
    /** @var EntityManager */
    private $entityManager;

    /** @var CanonicalizerInterface */
    private $canonicalizer;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    private $referrerAction;

    private $referrerAmount;

    private $referrerCurrencyCode;

    private $inviteeAction;

    private $inviteeAmount;

    private $inviteeCurrencyCode;

    public function __construct(
        EntityManager $entityManager,
        CanonicalizerInterface $canonicalizer,
        RepositoryInterface $customerRepository,
        $referrerAction,
        $referrerAmount,
        $referrerCurrencyCode,
        $inviteeAction,
        $inviteeAmount,
        $inviteeCurrencyCode
    )
    {
        $this->entityManager = $entityManager;
        $this->canonicalizer = $canonicalizer;
        $this->customerRepository = $customerRepository;
        $this->referrerAction = $referrerAction;
        $this->referrerAmount = $referrerAmount;
        $this->referrerCurrencyCode = $referrerCurrencyCode;
        $this->inviteeAction = $inviteeAction;
        $this->inviteeAmount = $inviteeAmount;
        $this->inviteeCurrencyCode = $inviteeCurrencyCode;
    }

    function execute(Reference $reference)
    {
        $credit = new Credit();
        $credit->setCustomer($reference->getInvitee());
        $credit->setAction($this->inviteeAction);
        $credit->setAmount($this->inviteeAction);
        $credit->setCurrencyCode($this->inviteeCurrencyCode);
        $this->entityManager->persist($credit);

        $customer = new Customer();
        $customer->setEmail($reference->getReferrerEmail());
        $customer->setEmailCanonical($this->canonicalizer->canonicalize($reference->getReferrerEmail()));
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $credit = new Credit();
        $credit->setCustomer($this->customerRepository->findOneBy(['id' => $customer->getId()]));
        $credit->setAction($this->referrerAction);
        $credit->setAmount($this->referrerAmount);
        $credit->setCurrencyCode($this->referrerCurrencyCode);
        $this->entityManager->persist($credit);

        $this->entityManager->flush();
    }
}
