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

Finish the installation by updating the database schema and installing assets:
```
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
$ bin/console cache:clear
```
