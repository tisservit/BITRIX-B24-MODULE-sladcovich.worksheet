<?php

namespace Sladcovich\Worksheet\Helpers;

use \Bitrix\Main\Loader;
use \CUserFieldEnum;

Loader::includeModule('sladcovich.worksheet');
Loader::includeModule('crm');

class UserFieldHelper
{
    /**
     * @var string - код пользовательского поля для CRM_COMPANY
     */
    protected static $UF_CUSTOMER_CONTRACT_STATUS = 'UF_CUSTOMER_CONTRACT_STATUS';

    /**
     * @var string - код пользовательского поля для CRM_CONTACT
     */
    protected static $UF_WORKER_STATUS = 'UF_WORKER_STATUS';

    /**
     * Получаем массив возможных значений свойств пользовательского поля "UF_CUSTOMER_CONTRACT_STATUS" (['ID' => 'XML_ID'])
     *
     * @return array
     */
    public static function getAllCustomerContractStatusValues()
    {
        $arOptions = [];

        $res = CUserFieldEnum::GetList([], ['USER_FIELD_NAME' => self::$UF_CUSTOMER_CONTRACT_STATUS]);
        while($row = $res->Fetch())
        {
            $arOptions[$row['ID']] = $row['XML_ID'];
        }

        return $arOptions;
    }

    /**
     * Получаем массив возможных значений свойств пользовательского поля "UF_WORKER_STATUS" (['ID' => 'XML_ID'])
     *
     * @return array
     */
    public static function getAllWorkerStatusValues()
    {
        $arOptions = [];

        $res = CUserFieldEnum::GetList([], ['USER_FIELD_NAME' => self::$UF_WORKER_STATUS]);
        while($row = $res->Fetch())
        {
            $arOptions[$row['ID']] = $row['XML_ID'];
        }

        return $arOptions;
    }
}
