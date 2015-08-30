<?php

/*
 * This file is part of TomasVotruba.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Interoperability\Contract\CommandBus;

use Interoperability\Contract\CommandBus\Command\CommandInterface;

interface CommandBusInterface
{
    /**
     * Handles a command.
     *
     * @param CommandInterface $command
     */
    function handle(CommandInterface $command);
}
