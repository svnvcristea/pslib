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
use Sugarcrm\PSLib\SugarKernel;
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

        try {
            $sugarKernel = new SugarKernel('cli', 'dev', false);
            $sugarKernel->boot();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
        $pBar->advance(2);

        /** @var \User $user */
        $user = $sugarKernel->getG('current_user');
        $user->getSystemUser();
        $modules = $sugarKernel->getG('mod_strings');
        $pBar->advance();

        $qrr = new Qrr();
        if ($input->getOption('yell')) {
            $showOutput = true;
            $output->writeln('QRR output: ');
        }
        $qrr->repairAndClearAll(['clearAll'], [$modules['LBL_ALL_MODULES']], true, $showOutput);
        $pBar->advance(5);

        $sugarKernel->shutdown();
        $pBar->finish();

        $elapsed = time() - $pBar->getStartTime();
        $output->writeln(' ');
        $output->writeln(" Executed in: $elapsed s");
    }
}
