<?php

/*
 * This file is part of the SugarCRM\PSLib file package.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sugarcrm\PSLib\Console\Command;

use Sugarcrm\PSLib\Entry;
use Sugarcrm\PSLib\Boot;
use Sugarcrm\PSLib\Modules\Administration\Qrr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class QRRCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('ps:qrr')
            ->setDescription('Run SugarCRM full Quick Repair and Rebuild')
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will output QRR messages'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pBar = new ProgressBar($output, 10);
        $pBar->start();
        $showOutput = false;

        $entry = new Entry('cli');
        $entry->terminateUnAuthorizedSapi();
        $pBar->advance();

        try {
            Boot::entryPoint();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
        $pBar->advance();

        /** @var \User $user */
        $user = Boot::get('current_user');
        $user->getSystemUser();
        $mod_strings = Boot::modString();
        $modules = $mod_strings['LBL_ALL_MODULES'];
        $pBar->advance();

        $qrr = new Qrr();
        if ($input->getOption('yell')) {
            $showOutput = true;
            $output->writeln('QRR output: ');
        }
        $qrr->repairAndClearAll(['clearAll'], [$modules], true, $showOutput);
        $pBar->advance(6);

        Boot::cleanup();
        /** @var \Database $db */
        $db = Boot::get('db');
        if ($db) {
            $db->disconnect();
        }
        $pBar->finish();

        $elapsed = time() - $pBar->getStartTime();
        $output->writeln(' ');
        $output->writeln(" Executed in: $elapsed s");
    }
}
