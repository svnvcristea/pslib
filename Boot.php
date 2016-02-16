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

use \SugarAutoLoader;
use \LoggerManager;
use \BeanFactory;

class Boot
{

    public static function entryPoint()
    {
        error_reporting(E_ERROR);

        global $sugar_config;
        global $current_user;
        global $beanList;
        global $locale;
        global $db;
        global $dictionary;

        require_once('include/entryPoint.php');
    }

    public static function modString()
    {
        global $mod_strings;
        require 'modules/Administration/language/en_us.lang.php';

        return $mod_strings;
    }

    public static function sugarApp()
    {
        require_once('include/utils/autoloader.php');
        require_once('include/SugarObjects/SugarConfig.php');
        require_once('include/SugarCache/SugarCache.php');
        require_once('include/MetaDataManager/MetaDataManager.php');

//        require_once('include/database/DBManagerFactory.php');
        SugarAutoLoader::init();
        $GLOBALS['log'] = LoggerManager::getLogger('SugarCRM');

    }

    public static function get($globalName)
    {
        return $GLOBALS[$globalName];
    }

    public static function cleanup()
    {
        sugar_cleanup(false);
    }
}
