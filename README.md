[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/workouse/sylius-digital-wallet-plugin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/workouse/sylius-digital-wallet-plugin/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/workouse/sylius-digital-wallet-plugin/badges/build.png?b=master)](https://scrutinizer-ci.com/g/workouse/sylius-digital-wallet-plugin/build-status/master)
## Workouse Digital Wallet Plugin
Digital wallet plugin for for Sylius E-Commerce. Adds a credit system feature to customers.

Screenshots:
<img src="https://github.com/workouse/SyliusDigitalWalletPlugin/blob/master/screenshot_1.png" width="100%">
<img src="https://github.com/workouse/SyliusDigitalWalletPlugin/blob/master/screenshot_2.png" width="100%">
<img src="https://github.com/workouse/SyliusDigitalWalletPlugin/blob/master/screenshot_3.png" width="100%">
<img src="https://github.com/workouse/SyliusDigitalWalletPlugin/blob/master/screenshot_4.png" width="100%">

## Installation
```bash
$ composer require workouse/sylius-digital-wallet-plugin
```
Add plugin dependencies to your `config/bundles.php` file:
```php
return [
    ...

    Workouse\SyliusDigitalWalletPlugin\WorkouseSyliusDigitalWalletPlugin::class => ['all' => true],
];
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    
    - { resource: "@WorkouseSyliusDigitalWalletPlugin/Resources/config/config.yml" }
```

Import routing in your `config/routes.yaml` file:

```yaml

# config/routes.yaml
...

workouse_digital_wallet_plugin:
    resource: "@WorkouseSyliusDigitalWalletPlugin/Resources/config/routing.yml"
```

Configuration in your `config/routes.yaml` file:
```yaml
#config/packages/workouse_digital_wallet.yml
workouse_digital_wallet:
    referrer:
        action: 'reference'
        amount: 500 #must be calculated as amount * 100 for fits sylius money format.
        currency_code: 'USD'
    invitee:
        action: 'reference'
        amount: 100
        currency_code: 'USD'
```

if want to associate with [referral-marketing-sylius](https://github.com/workouse/referral-marketing-sylius), you need to apply the setting below 
```yaml
#config/packages/workouse_referral_marketing.ym
workouse_referral_marketing:
    service: 'workouse_digital_wallet.promotion'
    
    ...
```

Finish the installation by updating the database schema and installing assets:
```
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
$ bin/console cache:clear
```

## Testing & running the plugin
```bash
$ composer install
$ cd tests/Application
$ yarn
$ yarn build
$ bin/console assets:install public -e test
$ bin/console doctrine:database:create -e test
$ bin/console doctrine:schema:create -e test
$ bin/console server:run 127.0.0.1:8080 -d public -e test
$ open http://localhost:8080
$ vendor/bin/behat
```
