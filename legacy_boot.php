<?php

/*
 * This file is part of the SugarCRM\PSLib file package.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    global $sugar_config;
    global $current_user;
    global $beanList;
    global $locale;
    global $db;
    global $dictionary;
    global $log;
    global $timedate;
    global $mod_strings;
    global $rootDir;

    require_once $rootDir.'/include/utils.php';
    require_once $rootDir.'/include/SugarCache/SugarCache.php';
    require_once $rootDir.'/include/SugarObjects/SugarConfig.php';
    require_once $rootDir.'/config.php';
    require_once $rootDir.'/include/modules.php';
    require_once $rootDir.'/modules/Administration/language/en_us.lang.php';
}
