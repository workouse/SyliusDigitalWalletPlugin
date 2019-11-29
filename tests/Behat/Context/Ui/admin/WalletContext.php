<?php

declare(strict_types=1);

namespace Tests\Workouse\SyliusDigitalWalletPlugin\Behat\Context\Ui\admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Tests\Workouse\SyliusDigitalWalletPlugin\Behat\Page\Admin\WalletNewPage;
use Webmozart\Assert\Assert;

class WalletContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var WalletNewPage */
    private $createPage;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
        WalletNewPage $createPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
        $this->createPage = $createPage;
    }

    /**
     * @When /^I go to the create credit with (this customer)$/
     */
    public function iGoToTheCreateCreditWith(CustomerInterface $customer)
    {
        $this->createPage->open(['customerId' => $customer->getId()]);
    }

    /**
     * @When I fill the Amount with :amount
     */
    public function iFillTheNameWith(string $amount): void
    {
        $this->resolveCurrentPage()->fillAmount($amount);

        $this->sharedStorage->set('amount', $amount);
    }

    /**
     * @When I fill the Currency Code with :amount
     */
    public function iFillTheCurrencyCodeWith(string $currencyCode): void
    {
        $this->resolveCurrentPage()->fillCurrencyCode($currencyCode);

        $this->sharedStorage->set('currency_code', $currencyCode);
    }

    /**
     * @When I fill the Action with :amount
     */
    public function iFillTheActionWith(string $action): void
    {
        $this->resolveCurrentPage()->fillAction($action);

        $this->sharedStorage->set('action', $action);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->resolveCurrentPage()->create();
    }

    /**
     * @Then I should be notified that the credit has been created
     */
    public function iShouldBeNotifiedThatNewRreditWasCreated(): void
    {
        $this->notificationChecker->checkNotification(
            'Credit has been successfully added.',
            NotificationType::success()
        );
    }

    /**
     * @Then I should be notified that :fields fields cannot be blank
     */
    public function iShouldBeNotifiedThatFieldsCannotBeBlank(string $fields): void
    {
        $fields = explode(',', $fields);

        foreach ($fields as $field) {
            Assert::true($this->resolveCurrentPage()->containsErrorWithMessage(sprintf(
                '%s cannot be blank.',
                trim($field)
            )));
        }
    }

    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
        ]);
    }
}
