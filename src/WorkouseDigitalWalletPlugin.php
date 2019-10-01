<?php

declare(strict_types=1);

namespace Workouse\DigitalWalletPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class WorkouseDigitalWalletPlugin extends Bundle
{
    use SyliusPluginTrait;
}
