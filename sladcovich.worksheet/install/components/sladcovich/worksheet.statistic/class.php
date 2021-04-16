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

use \Bitrix\Main\Engine\Contract\Controllerable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

use \Sladcovich\Worksheet\Entity\ORM\WorksheetTable;
use \Sladcovich\Worksheet\Entity\ORM\WorkerTable;

use \Sladcovich\Worksheet\Helpers\CrmEntityHelper;

Loader::includeModule('sladcovich.worksheet');
Loader::includeModule('crm');

Loc::loadMessages(__FILE__);

class WorksheetStatisticComponent extends CBitrixComponent implements Controllerable
{
    /* Базовые методы компонента */
    /**
     * Метод из интерфейса Controllerable для реализации AJAX
     *
     * @return array[][]
     */
    public function configureActions()
    {
        return [
            'test' => ['test' => []],
        ];
    }

    /**
     * Метод из наследуемого класса CBitrixComponent - Выполнение компонента
     *
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        $currentDatetimeFrom = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime(date('d.m.Y') . ' 00:00:00'));
        $currentDatetimeTo = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime(date('d.m.Y') . ' 23:59:59'));

        $this->arResult = self::getData($currentDatetimeFrom, $currentDatetimeTo);
        $this->includeComponentTemplate();
    }

    /* Пользовательские методы компонента */
    protected static function getData($currentDatetimeFrom, $currentDatetimeTo)
    {
        // Структура массива результатов
        $arResult = [
            'COMMON' => [
                'TOTAL' => 0
            ],
            'CUSTOMERS' => [
                'WORKSHEET_YES' => [
                    'DATA' => [],
                    'TOTAL' => [],
                ],
                'WORKSHEET_NO' => [
                    'DATA' => [],
                    'TOTAL' => [],
                ],
            ],
            'WORKERS' => [
                'WORKSHEET_YES' => [
                    'DATA' => [],
                    'TOTAL' => [],
                ],
                'WORKSHEET_NO' => [
                    'DATA' => [],
                    'TOTAL' => [],
                ],
            ]
        ];

        // Клиенты и рабочие у которых есть смены на сегодня
        $res = WorksheetTable::getList([
            'select' => ['ID', 'B24_COMPANY_ID', 'SEARCH_COMPANY'],
            'filter' => [
                'LOGIC' => 'AND',
                [
                    '>=DATETIME_START' => $currentDatetimeFrom
                ],
                [
                    '<=DATETIME_END' => $currentDatetimeTo
                ]
            ],
            'count_total' => true
        ]);
        while ($row = $res->fetch()) {
            $arResult['CUSTOMERS']['WORKSHEET_YES']['DATA'][$row['B24_COMPANY_ID']] = $row['SEARCH_COMPANY'];

            $subRes = WorkerTable::getList([
                'select' => ['B24_CONTACT_ID'],
                'filter' => ['WORKSHEET_ID' => $row['ID']],
            ]);
            while ($subRow = $subRes->fetch()) {
                $arResult['WORKERS']['WORKSHEET_YES']['DATA'][$subRow['B24_CONTACT_ID']] = CrmEntityHelper::getContactFIO($subRow['B24_CONTACT_ID']);
            }
        }

        $arResult['COMMON']['TOTAL'] = $res->getCount();

        $arResult['CUSTOMERS']['WORKSHEET_YES']['TOTAL'] = count($arResult['CUSTOMERS']['WORKSHEET_YES']['DATA']);
        $arResult['WORKERS']['WORKSHEET_YES']['TOTAL'] = count($arResult['WORKERS']['WORKSHEET_YES']['DATA']);

        // Клиенты и рабочие у которых нет смен на сегодня
        $allCustomers = CrmEntityHelper::getAllClientsCompanies(false, false);
        $allWorkers = CrmEntityHelper::getAllWorkersContacts(false);

        $arResult['CUSTOMERS']['WORKSHEET_NO']['DATA'] = array_diff($allCustomers, $arResult['CUSTOMERS']['WORKSHEET_YES']['DATA']);
        $arResult['WORKERS']['WORKSHEET_NO']['DATA'] = array_diff($allWorkers, $arResult['WORKERS']['WORKSHEET_YES']['DATA']);

        $arResult['CUSTOMERS']['WORKSHEET_NO']['TOTAL'] = count($arResult['CUSTOMERS']['WORKSHEET_NO']['DATA']);
        $arResult['WORKERS']['WORKSHEET_NO']['TOTAL'] = count($arResult['WORKERS']['WORKSHEET_NO']['DATA']);

        return $arResult;
    }

    /* Экшены компонента */
}