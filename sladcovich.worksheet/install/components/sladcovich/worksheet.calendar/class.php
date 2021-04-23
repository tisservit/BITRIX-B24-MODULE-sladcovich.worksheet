<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

/** @var CBitrixComponent $component */

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

use \Sladcovich\Worksheet\Entity\ORM\WorksheetTable;
use \Sladcovich\Worksheet\Entity\ORM\WorkerTable;

use \Sladcovich\Worksheet\Helpers\CrmEntityHelper;
use \Sladcovich\Worksheet\Helpers\UserFieldHelper;

Loader::includeModule('sladcovich.worksheet');
Loader::includeModule('crm');

Loc::loadMessages(__FILE__);

class WorksheetCalendarComponent extends CBitrixComponent
{
    /**
     * @var string - режим работы компонента
     */
    protected static $companyId = 0;

    /**
     * @var bool - является ли текущая компания клиентом
     */
    protected static $customer = false;

    /* Базовые методы компонента */
    /**
     * Метод из наследуемого класса CBitrixComponent - Обработка параметров компонента
     *
     * @param $arParams
     * @return array|void
     */
    public function onPrepareComponentParams($arParams)
    {
        if (isset($arParams['COMPANY_ID']) && $arParams['COMPANY_ID'] > 0) {
            self::$companyId = $arParams['COMPANY_ID'];
        }
    }

    /**
     * Метод из наследуемого класса CBitrixComponent - Выполнение компонента
     *
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        $companyId = self::$companyId;

        if ($companyId > 0) {
            $this->arResult['CUSTOMER_MODE'] = self::checkCustomerStatus($companyId);
        } else {
            $this->arResult['CUSTOMER_MODE'] = true;
        }

        $this->arResult['COMPANY_ID'] = intval(self::$companyId);
        $this->arResult['JSON']['EVENTS'] = self::getEventsForCalendar($companyId);
        $this->includeComponentTemplate();
    }

    /* Дополнительные методы компонента */
    /**
     * Получаем JSON представление рабочих смен для календаря
     *
     * @param int $companyId
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getEventsForCalendar($companyId = 0)
    {
        $json = [];

        // Загрузка всех событий
        if ($companyId === 0) {
            $res = WorksheetTable::getList([
                'select' => ['*']
            ]);
            // Загрузка событий конкретной компании
        } else {
            $res = WorksheetTable::getList([
                'select' => ['*'],
                'filter' => ['B24_COMPANY_ID' => $companyId]
            ]);
        }

        while ($row = $res->fetch()) {

            $workers = [];

            $subRes = WorkerTable::getList([
                'select' => ['B24_CONTACT_ID'],
                'filter' => ['WORKSHEET_ID' => $row['ID']],
                'count_total' => true
            ]);
            while ($subRow = $subRes->fetch()) {
                $workers[] = CrmEntityHelper::getContactFIO($subRow['B24_CONTACT_ID']);
            }
            $worksheetWorkersCount = $subRes->getCount();

            $json[] = [
                'id' => $row['ID'],
                'title' => CrmEntityHelper::getCompanyTitle($row['B24_COMPANY_ID']) . ' (' . $worksheetWorkersCount . ')',
                'start' => ConvertDateTime($row['DATETIME_START'], "YYYY-MM-DD HH:MI:SS", "ru"),
                'end' => ConvertDateTime($row['DATETIME_END'], "YYYY-MM-DD HH:MI:SS", "ru"),
                'workers' => $workers
            ];
        }

        return \Bitrix\Main\Web\Json::encode($json);
    }

    /**
     * Проверяем - является ли текущая компания клиентом, показать ли календарь
     *
     * @param $companyId
     * @return bool
     */
    public static function checkCustomerStatus($companyId)
    {
        global $USER_FIELD_MANAGER;
        $allOptions = UserFieldHelper::getAllCustomerContractStatusValues();

        $currentStatusValueId = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_COMPANY', 'UF_CUSTOMER_CONTRACT_STATUS', $companyId);
        if ($currentStatusValueId > 0) {
            if ($allOptions[$currentStatusValueId] == 'ACTIVE') {
                return true;
            } else {
                return false;
            }
        }
    }
}