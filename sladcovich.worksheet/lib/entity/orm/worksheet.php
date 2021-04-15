<?php

namespace Sladcovich\Worksheet\Entity\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\Loader;

Loader::includeModule('crm');

/**
 * @package Sladcovich\Worksheet
 */
class WorksheetTable extends Entity\DataManager
{
    /**
     * Return name of table
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_sladcovich_worksheet_entity_orm_worksheet';
    }

    /**
     *
     * @see http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=4803&LESSON_PATH=3913.5062.5748.4803
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID',                                               ['primary'=> true, 'autocomplete' => true]),

            new Entity\DatetimeField('DATETIME_START',                                  ['title' => 'Время начала смены']),
            new Entity\DatetimeField('DATETIME_END',                                    ['title' => 'Время окончания смены']),

            new Entity\IntegerField('B24_COMPANY_ID',                                   ['title' => 'Компания (Битрикс 24)']),
            new Entity\ReferenceField('B24_COMPANY', 'Bitrix\Crm\CompanyTable',         ['=this.B24_COMPANY_ID' => 'ref.ID']),

            new Entity\StringField('SEARCH_USER',                                       ['title' => 'ФИО пользователя (Битрикс 24)']),
            new Entity\StringField('SEARCH_COMPANY',                                    ['title' => 'Наименование компании']),
            new Entity\StringField('SEARCH_WORKERS',                                    ['title' => 'ФИО работников']),

            new Entity\IntegerField('B24_MODIFIED_BY_USER_ID',                          ['title' => 'Изменил пользователь (Битрикс 24)']),
            new Entity\ReferenceField('B24_MODIFIED_BY_USER', 'Bitrix\Main\UserTable',  ['=this.DEAL_ID_B24' => 'ref.ID']),

            new Entity\DatetimeField('DATETIME_MODIFY',                                 ['title' => 'Дата и время изменения']),
        ];
    }
}