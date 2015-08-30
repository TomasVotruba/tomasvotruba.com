<?php

/*
 * This file is part of TomasVotruba.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Interoperability\Adapter\CommandBus;

use Interoperability\Contract\CommandBus\Command\CommandInterface;
use Interoperability\Contract\CommandBus\CommandBusInterface;

final class SimpleBusCommandBusAdapter implements CommandBusInterface
{
    public function __construct()
    {
    }

    /**
     * Handles a command.
     *
     * @param CommandInterface $command
     */
    function handle(CommandInterface $command)
    {
        // TODO: Implement handle() method.
    }
}
