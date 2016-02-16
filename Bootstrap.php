<?php

/*
 * This file is part of the SugarCRM\PSLib file package.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sugarcrm\PSLib;

use Sugarcrm\PSLib\Console\Command;
use Symfony\Component\Console\Application;

class Bootstrap
{
    public static function commands()
    {
        $app = new Application('SugarCRM::PSLib', '7.6.3.0');
        $app->add(new Command\GreetCommand());
        $app->add(new Command\QRRCommand());
        $app->run();
    }
}
