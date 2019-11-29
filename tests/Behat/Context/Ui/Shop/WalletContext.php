<?php


namespace Tests\Workouse\SyliusDigitalWalletPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManager;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Tests\Workouse\SyliusDigitalWalletPlugin\Behat\Page\Shop\WalletRemovePage;
use Tests\Workouse\SyliusDigitalWalletPlugin\Behat\Page\Shop\WalletUsePage;
use Webmozart\Assert\Assert;
use Workouse\SyliusDigitalWalletPlugin\Entity\Credit;

class WalletContext implements Context
{

    /** @var DashboardPageInterface */
    private $dashboardPage;

    /** @var EntityManager */
    private $entityManager;

    /** @var WalletUsePage */
    private $walletUsePage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var WalletRemovePage */
    private $walletRemovePage;

    public function __construct(
        DashboardPageInterface $dashboardPage,
        EntityManager $entityManager,
        WalletUsePage $walletUsePage,
        NotificationCheckerInterface $notificationChecker,
        UserRepositoryInterface $userRepository,
        WalletRemovePage $walletRemovePage
    )
    {
        $this->dashboardPage = $dashboardPage;
        $this->entityManager = $entityManager;
        $this->walletUsePage = $walletUsePage;
        $this->notificationChecker = $notificationChecker;
        $this->userRepository = $userRepository;
        $this->walletRemovePage = $walletRemovePage;
    }

    /**
     * @When I go to the account page
     */
    public function iGoToTheAccountReferrer(): void
    {
        $this->dashboardPage->open();
    }

    /**
     * @When I see my credit
     */
    public function iSeeMyCredit()
    {
        $this->dashboardPage->hasCustomerCredit();
    }

    /**
     * @Given I have :amount :currency_code credit with :email
     */
    public function iHaveCreditWith($amount = 100, $currencyCode = "USD", $email)
    {
        $user = $this->userRepository->findOneByEmail($email);
        $credit = new Credit();
        $credit->setAmount($amount);
        $credit->setCurrencyCode($currencyCode);
        $credit->setCustomer($user->getCustomer());
        $credit->setAction("test");
        $this->entityManager->persist($credit);
        $this->entityManager->flush();
    }

    /**
     * @When I use my credit
     */
    public function iUseMyCredit()
    {
        $this->walletUsePage->tryToOpen();
    }

    /**
     * @Then I should be notified that the credit has been used
     */
    public function iShouldBeNotifiedThatNewRreditWasUsed(): void
    {
        $this->notificationChecker->checkNotification(
            'Balance Used',
            NotificationType::success()
        );
    }

    /**
     * @Then My cart's total should :total
     */
    public function myCartSTotalShould($total)
    {
        Assert::same($this->walletUsePage->getCartTotal(), $total);
    }

    /**
     * @When I remove my credit
     */
    public function iRemoveMyCredit()
    {
        $this->walletRemovePage->tryToOpen();
    }

    /**
     * @Then I should be notified that the credit has been removed
     */
    public function iShouldBeNotifiedThatNewRreditWasRemoved(): void
    {
        $this->notificationChecker->checkNotification(
            'Balance Removed',
            NotificationType::success()
        );
    }
}
