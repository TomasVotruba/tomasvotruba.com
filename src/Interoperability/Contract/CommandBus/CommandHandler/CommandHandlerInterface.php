<?php

/*
 * This file is part of Symfonisti.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace Interoperability\Contract\CommandBus\CommandHandler;

use Interoperability\Contract\CommandBus\Command\CommandInterface;

interface CommandHandlerInterface
{
    function handle(CommandInterface $command);
}
