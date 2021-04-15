<?php

namespace Sladcovich\Worksheet\Entity\ORM;

use Bitrix\Main\Entity;
use Bitrix\Main\Loader;

Loader::includeModule('crm');

/**
 * @package Sladcovich\Worksheet
 */
class WorkerTable extends Entity\DataManager
{
    /**
     * Return name of table
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_sladcovich_worksheet_entity_orm_worker';
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
            new Entity\IntegerField('ID',                                                           ['primary'=> true, 'autocomplete' => true]),

            new Entity\IntegerField('WORKSHEET_ID',                                                 ['title' => 'Рабочая смена']),
            new Entity\ReferenceField('WORKSHEET', 'Sladcovich\Worksheet\Entity\ORM\WorksheetTable',['=this.WORKSHEET_ID' => 'ref.ID']),

            new Entity\IntegerField('B24_CONTACT_ID',                                               ['title' => 'Контакт (Битрикс 24)']),
            new Entity\ReferenceField('B24_CONTACT', 'Bitrix\Crm\ContactTable',                     ['=this.B24_CONTACT_ID' => 'ref.ID']),

            new Entity\IntegerField('B24_MODIFIED_BY_USER_ID',                                      ['title' => 'Изменил пользователь (Битрикс 24)']),
            new Entity\ReferenceField('B24_MODIFIED_BY_USER', 'Bitrix\Main\UserTable',              ['=this.DEAL_ID_B24' => 'ref.ID']),

            new Entity\DatetimeField('DATETIME_MODIFY',                                             ['title' => 'Дата и время изменения']),
        ];
    }
}