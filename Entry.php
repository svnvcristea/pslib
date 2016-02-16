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

class Entry
{
    private $sugarEntryPoint;
    private $sapiName;
    private $authorizedSapiName;

    const SUGAR_ENTRY = 'sugarEntry';
    const SUGAR_ENTRY_POINT_TYPE = 'ENTRY_POINT_TYPE';
    const SAPI_CLI = 'cli';

    /**
     * SetUp Sugar entry point.
     * @param $sugarEntryPoint
     */
    public function __construct($sugarEntryPoint)
    {
        define(self::SUGAR_ENTRY, true);
        define(self::SUGAR_ENTRY_POINT_TYPE, $sugarEntryPoint);

        $this->setSugarEntryPoint($sugarEntryPoint);
        $this->setAuthorizedSapi(self::SAPI_CLI);
        $this->setSapiName();
    }

    private function setSugarEntryPoint($sugarEntryPoint)
    {
        $this->sugarEntryPoint = $sugarEntryPoint;
    }

    public function getSugarEntryPoint()
    {
        return $this->sugarEntryPoint;
    }

    /**
     * Die with status code 403 Forbidden as the request was understood but is refusing to fulfill it.
     * @param $message
     */
    private function sugarDie($message)
    {
        @header("HTTP/1.0 403 Forbidden");
        @header("Status: 403 Forbidden");
        sugar_cleanup();
        echo $message;
        die();
    }

    /**
     * Set the type of interface between web server and PHP
     */
    private function setSapiName()
    {
        $this->sapiName = php_sapi_name();
    }

    /**
     * @param $authorizedSapiName
     */
    public function setAuthorizedSapi($authorizedSapiName)
    {
        $this->authorizedSapiName = $authorizedSapiName;
    }

    /**
     * @return false/die()
     */
    public function terminateUnAuthorizedSapi()
    {
        if (substr($this->sapiName, 0, 3) != $this->authorizedSapiName) {
            $this->sugarDie("this script is " . $this->authorizedSapiName . "only.");
        }

        return false;
    }
}
