<?php

namespace Sladcovich\Worksheet\Helpers;

use \Bitrix\Main\Loader;
# NON USED use \Bitrix\Crm\LeadTable;
# NON USED use \Bitrix\Crm\DealTable;
use \Bitrix\Crm\ContactTable;
use \Bitrix\Crm\CompanyTable;

use \Sladcovich\Worksheet\Helpers\UserFieldHelper;

Loader::includeModule('sladcovich.worksheet');
Loader::includeModule('crm');

class CrmEntityHelper
{
    /* Лид */
    # NON USED

    /* Сделка */
    # NON USED

    /* Контакт */
    /**
     * Получаем ФИО контакта
     *
     * @param $id
     * @return string
     */
    public static function getContactFIO($id)
    {
        if ($id > 0) {
            $res = ContactTable::getList([
                'select' => ['LAST_NAME', 'NAME', 'SECOND_NAME'],
                'filter' => ['ID' => $id],
                'limit' => 1
            ]);
            while ($row = $res->fetch()) {
                return strval($row['LAST_NAME'] . ' ' . $row['NAME'] . ' ' . $row['SECOND_NAME']);
            }
        }
    }

    /**
     * Получаем контакты со статусом договора - ACTIVE
     *
     * @return array
     */
    public function getAllWorkersContacts()
    {
        global $USER_FIELD_MANAGER;
        $arWorkerContacts = [];
        $allOptions = UserFieldHelper::getAllWorkerStatusValues();

        $res = \Bitrix\Crm\ContactTable::getList([
            'select' => ['ID', 'LAST_NAME', 'NAME', 'SECOND_NAME'],
            'order' => ['ID']
        ]);

        while ($row = $res->fetch()) {
            $currentStatusValueId = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_CONTACT', 'UF_WORKER_STATUS', $row['ID']);
            if ($currentStatusValueId > 0) {
                if ($allOptions[$currentStatusValueId] == 'ACTIVE') {
                    $arWorkerContacts[] = [
                        'id' => $row['ID'],
                        'text' => ($row['LAST_NAME'] . ' ' . $row['NAME'] . ' ' . $row['SECOND_NAME'])
                    ];
                }
            }
        }

        return $arWorkerContacts;
    }

    /* Компания */
    /**
     * Получаем наименование компании
     *
     * @param $id
     * @return string
     */
    public static function getCompanyTitle($id)
    {
        if ($id > 0) {
            $res = CompanyTable::getList([
                'select' => ['TITLE'],
                'filter' => ['ID' => $id],
                'limit' => 1
            ]);
            while ($row = $res->fetch()) {
                return strval($row['TITLE']);
            }
        }
    }

    /**
     * Получаем компании со статусом договора - ACTIVE
     *
     * @return array
     */
    public function getAllClientsCompanies()
    {
        global $USER_FIELD_MANAGER;
        $arClientCompanies = [];
        $arClientCompanies[] = ['id' => 'delete', 'text' => 'Выберите клиента'];
        $allOptions = UserFieldHelper::getAllCustomerContractStatusValues();

        $res = \Bitrix\Crm\CompanyTable::getList([
            'select' => ['ID', 'TITLE'],
            'order' => ['ID']
        ]);

        while ($row = $res->fetch()) {
            $currentStatusValueId = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_COMPANY', 'UF_CUSTOMER_CONTRACT_STATUS', $row['ID']);
            if ($currentStatusValueId > 0) {
                if ($allOptions[$currentStatusValueId] == 'ACTIVE') {
                    $arClientCompanies[] = [
                        'id' => $row['ID'],
                        'text' => $row['TITLE']
                    ];
                }
            }
        }

        return $arClientCompanies;
    }
}