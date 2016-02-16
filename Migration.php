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

class Migration
{
    /** @var  DBManager $dbManager */
    private $dbManager;

    /**
     * Constructor.
     * @param DBManager $db
     */
    public function __construct(DBManager $db = null)
    {
        $this->setDbManager($db);
    }

    /**
     * @param DBManager $db
     */
    public function setDbManager(DBManager $db = null)
    {

        if (!$db) {
            $db = $GLOBALS['db'];
        }
        $this->dbManager = $db;
    }

    /**
     * Returns The DBManager
     * @return DBManager
     */
    public function getDbManager()
    {
        return $this->db;
    }

    /**
     * Run all .sql files founded at $sqlDirPath
     * @param string $sqlDirPath
     * @param bool $dieOnError
     */
    public function migrateSQL($sqlDirPath, $dieOnError = false)
    {
        foreach (glob($sqlDirPath . '/*.sql') as $sqlFile) {
            $sql = file_get_contents($sqlFile);
            $this->dbManager->query($sql, $dieOnError, 'Cannot run SQL query from ' . $sqlFile);
        }
    }

    /**
     * @param $sql
     * @param bool|false $dieOnError
     * @return bool|resource
     */
    public function query($sql, $dieOnError = false)
    {
        return $this->dbManager->query($sql, $dieOnError, 'Cannot run SQL query: ' . $sql);
    }

    /**
     * @param $metadata
     * @param $module
     * @return bool|resource
     */
    public function updateDashboardsMetadata($metadata, $module)
    {
        return $this->dbManager->query(
            sprintf(
                "
                UPDATE dashboards SET
                    metadata = '%s'
                WHERE
                    deleted = 0
                    AND name = 'LBL_DEFAULT_DASHBOARD_TITLE'
                    AND dashboard_module = '$module'
                    AND view_name = 'record'
                    AND dashboard_type = 'dashboard'
                ",
                $this->dbManager->quote(json_encode($metadata))
            )
        );
    }
}
