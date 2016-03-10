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

use SugarAutoLoader;
use BeanFactory;
use SugarApplication;
use LoggerManager;
use DBManagerFactory;
use Localization;
use Administration;
use TimeDate;
use Sugarcrm\PSLib\Entry;
use Composer\Autoload\ClassLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

class SugarKernel
{
    protected $container;
    protected $rootDir;
    protected $environment;
    protected $debug;
    protected $booted = false;
    protected $name;
    protected $startTime;

    public $db;
    public $locale;

    const VERSION = '7.6.3';
    const MAJOR_VERSION = 7;
    const MINOR_VERSION = 6;
    const RELEASE_VERSION = 3;
    const EXTRA_VERSION = '';
    const VENDOR_PACK_NAME = 'sugarcrm-ps/pslib';

    public function __construct($entryPoint, $environment, $debug)
    {
        $entry = new Entry($entryPoint);
        $entry->terminateUnAuthorizedSapi();

        $this->environment = $environment;
        $this->debug = (bool)$debug;
        $this->rootDir = $this->getRootDir();
        $this->name = $this->getName();

        if ($this->debug) {
            $this->startTime = microtime(true);
        } else {
            error_reporting(E_ERROR);
        }

    }

    public function initLoader(ClassLoader $loader = null)
    {
        if (!$loader) {
            $loader = require __DIR__ . '/../../autoload.php';
        }
        $mapping = array(
            'SugarAutoLoader' => 'include/utils/autoloader.php',
            'SugarConfig' => 'include/SugarObjects/SugarConfig.php',
        );
        $loader->addClassMap($mapping);
        $loader->add('', 'include/utils.php');

        return $loader;
    }

    public function boot()
    {
        $loader = $this->initLoader();
        $loader->register();

        require_once $this->getRootDir().'/vendor/'.self::VENDOR_PACK_NAME.'/legacy_boot.php';

        SugarAutoLoader::init();
//        InputValidation::initService();

        $this->container = new ContainerBuilder();
//        $this->container->register('logger', 'LoggerManager::getLogger');

        $log = LoggerManager::getLogger('SugarCRM');
        SugarApplication::preLoadLanguages();
        $timedate = TimeDate::getInstance();

        $db = DBManagerFactory::getInstance();
        $db->resetQueryCount();
        $locale = Localization::getObject();

        $current_user = BeanFactory::getBean('Users');
        $current_entity = null;
        $system_config = Administration::getSettings();

        $this->booted = true;
    }

    public function bootFullEntryPoint()
    {
        global $sugar_config;
        global $current_user;
        global $beanList;
        global $locale;
        global $db;
        global $dictionary;

        require_once('include/entryPoint.php');
    }

    protected function initializeContainer()
    {
        $class = $this->getContainerClass();
        $cache = new ConfigCache($this->getCacheDir() . '/' . $class . '.php', $this->debug);
        $fresh = true;
        if (!$cache->isFresh()) {
            $container = $this->buildContainer();
            $container->compile();
            $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

            $fresh = false;
        }

        require_once $cache->getPath();

        $this->container = new $class();
        $this->container->set('kernel', $this);

        if (!$fresh && $this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('kernel.cache_dir'));
        }
    }


    public static function getUser()
    {
        return BeanFactory::getBean('Users');
    }

    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('/vendor/'.self::VENDOR_PACK_NAME, '', dirname($r->getFileName()));
        }

        return $this->rootDir;
    }

    public function getName()
    {
        if (null === $this->name) {
            $this->name = preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->rootDir));
        }

        return $this->name;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function shutdown()
    {
        if (false === $this->booted) {
            return;
        }

        $this->booted = false;
        sugar_cleanup(false);
        $this->container = null;
    }

    public function getG($varName)
    {
        return $GLOBALS[$varName];
    }
}
