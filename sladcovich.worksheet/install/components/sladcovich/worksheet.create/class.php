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

class WorksheetCreateComponent extends CBitrixComponent implements Controllerable
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
        $this->arResult['CUSTOMERS_COMPANIES'] = CrmEntityHelper::getAllClientsCompanies();
        $this->arResult['WORKERS_CONTACTS'] = CrmEntityHelper::getAllWorkersContacts();
        $this->includeComponentTemplate();
    }

    /* Экшены компонента */
    /**
     * Добавляем новую смену
     *
     * @param $datetimeFrom
     * @param $datetimeTo
     * @param $clientCompany
     * @param $workersContacts
     * @return mixed
     * @throws Exception
     */
    public function createNewWorksheetAction($datetimeFrom, $datetimeTo, $clientCompany, $workersContacts)
    {
        global $USER;
        $rsUser = CUser::GetByID($USER->GetID());
        $arUser = $rsUser->Fetch();
        $userFIO = ($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']);

        $datetimeFrom = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($datetimeFrom));
        $datetimeTo = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($datetimeTo));
        $current = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime());

        $companyTitle = CrmEntityHelper::getCompanyTitle($clientCompany);
        $arWorkersFIO = [];
        $arNewWorkersId = [];

        $workersFIO = '';
        foreach ($workersContacts as $workerID) {
            $workersFIO = $workersFIO . CrmEntityHelper::getContactFIO($workerID) . ' ';
        }

        $res = WorksheetTable::add([
            'DATETIME_START' => $datetimeFrom,
            'DATETIME_END' => $datetimeTo,
            'B24_COMPANY_ID' => $clientCompany,
            'B24_MODIFIED_BY_USER_ID' => $USER->GetID(),
            'DATETIME_MODIFY' => $current,
            'SEARCH_USER' => $userFIO,
            'SEARCH_COMPANY' => $companyTitle,
            'SEARCH_WORKERS' => $workersFIO
        ]);

        if ($res->isSuccess()) {

            $newWorksheetId = $res->getId();

            foreach ($workersContacts as $worker) {
                $res = WorkerTable::add([
                    'WORKSHEET_ID' => $newWorksheetId,
                    'B24_CONTACT_ID' => intval($worker),
                    'B24_MODIFIED_BY_USER_ID' => $USER->GetID(),
                    'DATETIME_MODIFY' => $current,
                ]);

                if ($res->isSuccess()) {

                    $newWorkerId = $res->getId();
                    $arNewWorkersId[] = $newWorkerId;

                }
                $arWorkersFIO[] = CrmEntityHelper::getContactFIO($worker);
            }
        }

        return [
            'DATETIME_FROM' => $datetimeFrom,
            'DATETIME_TO' => $datetimeTo,
            'COMPANY_TITLE' => $companyTitle,
            'WORKERS_FIO' => $arWorkersFIO
        ];
    }
}