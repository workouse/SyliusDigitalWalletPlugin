## digital-wallet-sylius
Digital wallet plugin for for Sylius E-Commerce. Adds a credit system feature to customers.


## Installation
```bash
$ composer require workouse/digital-wallet-sylius
```
Add plugin dependencies to your `config/bundles.php` file:
```php
return [
    ...

    Workouse\DigitalWalletPlugin\WorkouseDigitalWalletPlugin::class => ['all' => true],
];
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    
    - { resource: "@WorkouseDigitalWalletPlugin/Resources/config/config.yml" }
```

Import routing in your `config/routes.yaml` file:

```yaml

# config/routes.yaml
...

workouse_digital_wallet_plugin:
    resource: "@WorkouseDigitalWalletPlugin/Resources/config/routing.yml"
```

Configuration in your `config/routes.yaml` file:
```yaml
#config/packages/workouse_digital_wallet.yml
workouse_digital_wallet:
    referrer:
        action: 'reference'
        amount: 500
        currency_code: 'TRY'
    invitee:
        action: 'reference'
        amount: 100
        currency_code: 'TRY'
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
