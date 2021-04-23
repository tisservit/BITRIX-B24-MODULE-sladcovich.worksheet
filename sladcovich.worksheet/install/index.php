<?php
/**
 * @global Bitrix\Main\Application|CMain $APPLICATION
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== TRUE)
{
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\IO\Directory;

/* next only meant to search here, or we probably not found it, first message include this lang file, and store it at Loc::$includedFiles */
Loc::loadMessages(__FILE__);

/**
 * Main class for 1C-Bitrix module
 * @link http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2824&LESSON_PATH=3913.4609.2824
 */
Class sladcovich_worksheet extends \CModule
{
    // @FIXME: code style must be as is for this string
    // bitrix marketplace not support another code style for module id
    /** @var string */
    public $MODULE_ID;
    /** @var int */
    public $MODULE_SORT = -1; // that's means first point after main
    /** @var string */
    public $MODULE_VERSION;
    /** @var string */
    public $MODULE_VERSION_DATE;
    /** @var string */
    public $MODULE_NAME;
    /** @var string */
    public $MODULE_DESCRIPTION;
    /** @var string */
    public $MODULE_GROUP_RIGHTS;
    /** @var string */
    public $PARTNER_NAME;
    /** @var string */
    public $PARTNER_URI;

    /**
     * constructor
     */
    public function __construct()
    {
        // Set version from specific file. It's required by bitrix marketplace
        $this->setVersion();

        $this->MODULE_ID = Loc::getMessage('SLADCOVICH_WORKSHEET_MODULE_ID');
        $this->MODULE_NAME = Loc::getMessage('SLADCOVICH_WORKSHEET_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('SLADCOVICH_WORKSHEET_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';

        // @FIXME: there is not available use D7 localisation now
        // because bitrix module update system not support d7 as localization phrases
        $this->PARTNER_NAME = GetMessage('SLADCOVICH_WORKSHEET_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('SLADCOVICH_WORKSHEET_MODULE_PARTNER_URL');
    }

    /**
     * Set module version
     *
     * @throws \Exception
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    private function setVersion()
    {
        if (!file_exists(__DIR__ . "/version.php"))
        {
            throw new FileNotFoundException(__DIR__ . "/version.php");
        }
        /** @var array Should be reassigned at file version.php */
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        if (!isset($arModuleVersion['VERSION']) || !isset($arModuleVersion['VERSION_DATE']))
        {
            throw new Exception('VERSION NUMBER OF VERSION DATE NOT SPECIFIED');
        }

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    /**
     * Installation steps, and register it at system.
     *
     * @return bool
     */
    public function DoInstall()
    {
        if (!$this->InstallDB()) {
            return false; // break installation with error
        }
        if (!$this->InstallFiles()) {
            return false; // break installation with error
        }
        if (!$this->InstallEvents()) {
            return false; // break installation with error
        }
        ModuleManager::registerModule($this->MODULE_ID); // register module in system
    }

    /**
     * Uninstall module step by step, and remove it from system.
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public function DoUninstall()
    {
        if (!$this->UnInstallDB()) {
            return false; // break uninstallation with error
        }
        if (!$this->UnInstallFiles()) {
            return false; // break uninstallation with error
        }
        if (!$this->UnInstallEvents()) {
            return false; // break uninstallation with error
        }

        ModuleManager::unRegisterModule($this->MODULE_ID); // register module in system
    }

    /**
     * Install tables for each used database
     *
     * @return bool
     */
    public function InstallDB()
    {
        global $DB;
        // careful we must have instruction CREATE TABLE IF NOT EXISTS for tables in sql file
        // or this rule for each table
        // if( !$DB->Query("SELECT 'x' FROM a_test WHERE 1=0", true) )
        $DB->RunSQLBatch(__DIR__."/db/".strtolower($DB->type)."/install.sql");

        return true;
    }

    /**
     * Uninstall tables for each used database
     *
     * @param bool $save Save tables and data. Do not truncate or remove tables.
     * @return bool
     */
    public function UnInstallDB()
    {
        global $DB;
        $DB->RunSQLBatch(__DIR__."/db/".strtolower($DB->type)."/uninstall.sql");

        return true;
    }

    /**
     * Install files
     *
     * @return bool
     */
    public function InstallFiles()
    {
        // Добавление css и js
        CopyDirFiles(__DIR__ . '/dist/', $_SERVER['DOCUMENT_ROOT'] . '/local/dist/sladcovich/', true, true);

        // Добавление компонентов sladcovich
        CopyDirFiles(__DIR__ . '/components/sladcovich/worksheet.calendar/', $_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.calendar/', true, true);
        CopyDirFiles(__DIR__ . '/components/sladcovich/worksheet.create/', $_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.create/', true, true);
        CopyDirFiles(__DIR__ . '/components/sladcovich/worksheet.registry/', $_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.registry/', true, true);
        CopyDirFiles(__DIR__ . '/components/sladcovich/worksheet.statistic/', $_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.statistic/', true, true);

        // Добавление раздела сайта
        CopyDirFiles(__DIR__ . '/worksheet/', $_SERVER['DOCUMENT_ROOT'] . '/worksheet/', true, true);

        // Добавление шаблонов компонентов Bitrix
        CopyDirFiles(__DIR__ . '/templates/bitrix24/components/bitrix/main.ui.filter/worksheet_registry_filter/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/main.ui.filter/worksheet_registry_filter/', true, true);
        CopyDirFiles(__DIR__ . '/templates/bitrix24/components/bitrix/system.field.view/sladcovich_user/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_user/', true, true);
        CopyDirFiles(__DIR__ . '/templates/bitrix24/components/bitrix/system.field.view/sladcovich_crm_company/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_crm_company/', true, true);
        CopyDirFiles(__DIR__ . '/templates/bitrix24/components/bitrix/system.field.view/sladcovich_crm_contact_workers/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_crm_contact_workers/', true, true);
        CopyDirFiles(__DIR__ . '/templates/bitrix24/components/bitrix/system.field.view/sladcovich_grid_row_commands/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_grid_row_commands/', true, true);

        // Добавления js в Bitrix
        CopyDirFiles(__DIR__ . '/bitrix/js/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/', true, true);

        return true;
    }

    /**
     * Uninstall files
     *
     * @return bool
     */
    public function UnInstallFiles()
    {
        // Удаление css и js
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/dist/sladcovich/');

        // Удаление компонентов sladcovich
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.calendar/');
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.create/');
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.registry/');
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/components/sladcovich/worksheet.statistic/');

        // Удаление раздела сайта
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/worksheet/');

        // Удаление шаблонов компонентов Bitrix
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/main.ui.filter/worksheet_registry_filter/');
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_user/');
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_crm_company/');
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_crm_contact_workers/');
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/bitrix24/components/bitrix/system.field.view/sladcovich_grid_row_commands/');

        // Удаление js в Bitrix
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/worksheet/');

        return true;
    }

    /**
     * Install events
     *
     * @return bool
     */
    public function InstallEvents()
    {
        RegisterModuleDependences('main', 'onProlog', $this->MODULE_ID, '\\Sladcovich\\Worksheet\\Helpers\\EventHelper', 'addTabToCompanyCard', 1);

        return true;
    }

    /**
     * Uninstall events
     *
     * @return bool
     */
    public function UnInstallEvents()
    {
        UnRegisterModuleDependences('main', 'onProlog', $this->MODULE_ID, '\\Sladcovich\\Worksheet\\Helpers\\EventHelper', 'addTabToCompanyCard', 1);

        return true;
    }
}

