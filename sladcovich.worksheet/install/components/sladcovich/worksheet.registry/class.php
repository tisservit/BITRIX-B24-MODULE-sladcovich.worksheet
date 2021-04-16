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

class WorksheetRegistryComponent extends CBitrixComponent implements Controllerable
{
    protected $id = 'worksheet_registry';
    protected $gridTemplate = '';//'worksheet_registry_grid'
    protected $filterTemplate = 'worksheet_registry_filter';

    protected $arFilter;
    protected $arSort;
    protected $navObject;
    protected $navParams;

    /* Базовые методы компонента */
    /**
     * Метод из интерфейса Controllerable для реализации AJAX
     *
     * @return array[][]
     */
    public function configureActions()
    {
        return [
            'worksheetCopy' => ['worksheetCopy' => []],
            'worksheetUpdate' => ['worksheetUpdate' => []],
            'worksheetDelete' => ['worksheetDelete' => []],
            'getWorksheetById' => ['getWorksheetById' => []],
        ];
    }

    /**
     * Метод из наследуемого класса CBitrixComponent - Выполнение компонента
     *
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        $this->getGridOptions();
        $dataForGrid = $this->collectDataForGrid();

        $arResult = [
            'COMMON' => [
                'ID' => $this->id
            ],
            'FILTER' => [
                'TEMPLATE' => $this->filterTemplate,
                'COLUMNS' => self::getFilterColumns()
            ],
            'GRID' => [
                'TEMPLATE' => $this->gridTemplate,
                'COLUMNS' => self::getGridColumns(),
                'DATA' => $dataForGrid['DATA'],
                'TOTAL_ROWS_COUNT' => $dataForGrid['TOTAL_ROWS_COUNT'],
                'NAV_OBJECT' => $this->navObject,
            ]
        ];

        $arResult['COMMON']['CUSTOMERS_COMPANIES'] = CrmEntityHelper::getAllClientsCompanies(false);
        $arResult['COMMON']['WORKERS_CONTACTS'] = CrmEntityHelper::getAllWorkersContacts();

        $this->arResult = $arResult;
        unset($arResult);
        $this->includeComponentTemplate();
    }

    /* Пользовательские методы компонента */
    /**
     * Получаем колонки для фильтра
     *
     * @return array
     */
    protected static function getFilterColumns()
    {
        $columns = [
            [
                'id' => 'DATETIME_START',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_DATETIME_START'),
                'type' => 'datetime',
                'time' => true,
                'default' => true,
            ],
            [
                'id' => 'DATETIME_END',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_DATETIME_END'),
                'type' => 'datetime',
                'time' => true,
                'default' => true,
            ],
            [
                'id' => 'SEARCH_COMPANY',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_COMPANY'),
                'type' => 'text',
                'default' => true,
            ],
            [
                'id' => 'SEARCH_WORKERS',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_WORKERS'),
                'type' => 'text',
                'default' => true,
            ],
            [
                'id' => 'SEARCH_USER',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_RESPONSIBLE_MODIFY'),
                'type' => 'text',
                'default' => true,
            ],
            [
                'id' => 'DATETIME_MODIFY',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_DATETIME_RESPONSIBLE_MODIFY_DATETIME'),
                'type' => 'datetime',
                'time' => true,
                'default' => true,
            ],
        ];

        return $columns;
    }

    /**
     * Получаем колонки для грида
     *
     * @return array[]
     */
    protected static function getGridColumns()
    {
        $columns = [
            [
                'id' => 'COMMANDS',
                'name' => '',
                'sort' => false,
                'default' => true,
                'width' => 20
            ],
            [
                'id' => 'DATETIME_START',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_DATETIME_START'),
                'sort' => 'DATETIME_START',
                'default' => true,
            ],
            [
                'id' => 'DATETIME_END',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_DATETIME_END'),
                'sort' => 'DATETIME_END',
                'default' => true,
            ],
            [
                'id' => 'COMPANY',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_COMPANY'),
                'sort' => false,
                'default' => true,
            ],
            [
                'id' => 'WORKERS',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_WORKERS'),
                'sort' => false,
                'default' => true,
            ],
            [
                'id' => 'RESPONSIBLE_MODIFY',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_RESPONSIBLE_MODIFY'),
                'sort' => false,
                'default' => true,
            ],
            [
                'id' => 'DATETIME_MODIFY',
                'name' => Loc::getMessage('SLADCOVICH_WORKSHEET_REGISTRY_DATETIME_RESPONSIBLE_MODIFY_DATETIME'),
                'sort' => 'DATETIME_MODIFY',
                'default' => true,
            ],
        ];

        return $columns;
    }

    protected function collectDataForGrid()
    {
        $dataForGrid = [
            'DATA' => [],
            'TOTAL_ROWS_COUNT' => 0
        ];

        $res = WorksheetTable::getList([
            'select' => ['*'],
            'filter' => $this->arFilter,
            'offset' => $this->navObject->getOffset(),
            'limit' => $this->navObject->getLimit(),
            'order' => $this->arSort['sort'],
            'count_total' => true
        ]);

        while ($row = $res->fetch()) {

            $arWorkersId = [];

            $subRes = WorkerTable::getList([
                'select' => ['B24_CONTACT_ID'],
                'filter' => ['WORKSHEET_ID' => $row['ID']],
                'order' => ['ID']
            ]);
            while ($subRow = $subRes->fetch()) {
                $arWorkersId[] = $subRow['B24_CONTACT_ID'];
            }

            $dataForGrid['DATA'][] = [
                'data' => [
                    'COMMANDS' => self::createField(
                        [
                            'ID' => $row['ID']
                        ],
                        'sladcovich_grid_row_commands'
                    ),
                    'DATETIME_START' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($row['DATETIME_START'])),
                    'DATETIME_END' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($row['DATETIME_END'])),
                    'COMPANY' => self::createField(
                        [
                            'ID' => $row['ID'],
                            'COMPANY_ID' => $row['B24_COMPANY_ID']
                        ],
                        'sladcovich_crm_company'
                    ),
                    'WORKERS' => self::createField(
                        [
                            'ID' => $row['ID'],
                            'WORKERS_ID' => $arWorkersId
                        ],
                        'sladcovich_crm_contact_workers'
                    ),
                    'RESPONSIBLE_MODIFY' => self::createField(
                        [
                            'ID' => $row['ID'],
                            'USER_ID' => $row['B24_MODIFIED_BY_USER_ID']
                        ],
                        'sladcovich_user'
                    ),
                    'DATETIME_MODIFY' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($row['DATETIME_MODIFY'])),
                ]
            ];
        }

        $dataForGrid['TOTAL_ROWS_COUNT'] = $res->getCount();
        $this->navObject->setRecordCount($dataForGrid['TOTAL_ROWS_COUNT']);

        return $dataForGrid;
    }

    protected function getGridOptions()
    {
        $grid_options = new Bitrix\Main\Grid\Options($this->id);
        $this->arSort = $grid_options->GetSorting(['sort' => ['ID' => 'ASC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
        $this->navParams = $grid_options->GetNavParams();

        $this->navObject = new Bitrix\Main\UI\PageNavigation($this->id);
        $this->navObject->allowAllRecords(true)
            ->setPageSize($this->navParams['nPageSize'] ? $this->navParams['nPageSize'] : 20)
            ->initFromUri();

        $filterOption = new Bitrix\Main\UI\Filter\Options($this->id);
        $filterData = $filterOption->getFilter(self::getFilterColumns());
        $filter = [];

        foreach ($filterData as $k => $v) {

            if ($k == 'SEARCH_WORKERS' || $k == 'SEARCH_USER' || $k == 'SEARCH_COMPANY') {
                $filter[$k][] = '%' . $v . '%';
            }

            if ($k == 'DATETIME_START') {
                $filter[] = [
                    '>=DATETIME_START' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($v)),
                ];
            }

            if ($k == 'DATETIME_END') {
                $filter[] = [
                    '>=DATETIME_END' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($v)),
                ];
            }

            if ($k == 'DATETIME_MODIFY') {
                $filter[] = [
                    'DATETIME_MODIFY' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($v)),
                ];
            }

            if ($k == 'FIND') {
                if (strlen($v) > 0) {
                    $filter[] = [
                        'LOGIC' => 'OR',
                        [
                            'SEARCH_COMPANY' => '%' . $filterData['FIND'] . '%'
                        ],
                        [
                            'SEARCH_WORKERS' => '%' . $filterData['FIND'] . '%'
                        ],
                    ];
                }
            }
        }

        $this->arFilter = $filter;
        unset($filter);
    }

    protected static function createField($arVars = array(), $type = '')
    {
        ob_start();
        global $APPLICATION;
        switch ($type) {
            case 'sladcovich_user':
                $APPLICATION->IncludeComponent(
                    'bitrix:system.field.view',
                    'sladcovich_user',
                    [
                        'bVarsFromForm' => [],
                        'arUserField' => [
                            'USER_TYPE' => 'sladcovich_user',
                            'PROPERTY_VALUE_LINK' => '',
                            'VALUE' => [
                                'ID' => $arVars['ID'],
                                'USER_ID' => $arVars['USER_ID'],
                                'USER_FIO' => self::getUserFIO($arVars['USER_ID'])
                            ],
                        ],
                    ],
                    null,
                    ['HIDE_ICONS' => 'Y']
                );
                break;
            case 'sladcovich_crm_company':
                $companyTitle = CrmEntityHelper::getCompanyTitle($arVars['COMPANY_ID']);
                $APPLICATION->IncludeComponent(
                    'bitrix:system.field.view',
                    'sladcovich_crm_company',
                    [
                        'bVarsFromForm' => [],
                        'arUserField' => [
                            'USER_TYPE' => 'sladcovich_crm_company',
                            'PROPERTY_VALUE_LINK' => '',
                            'VALUE' => [
                                'ID' => $arVars['ID'],
                                'COMPANY_ID' => $arVars['COMPANY_ID'],
                                'COMPANY_TITLE' => $companyTitle
                            ],
                        ],
                    ],
                    null,
                    ['HIDE_ICONS' => 'Y']
                );
                break;
            case 'sladcovich_crm_contact_workers':
                $arWorkers = [];
                foreach ($arVars['WORKERS_ID'] as $workerID) {
                    $arWorkers[$workerID] = CrmEntityHelper::getContactFIO($workerID);
                }
                $APPLICATION->IncludeComponent(
                    'bitrix:system.field.view',
                    'sladcovich_crm_contact_workers',
                    [
                        'bVarsFromForm' => [],
                        'arUserField' => [
                            'USER_TYPE' => 'sladcovich_crm_contact_workers',
                            'PROPERTY_VALUE_LINK' => '',
                            'VALUE' => [
                                'ID' => $arVars['ID'],
                                'WORKERS' => $arWorkers,
                            ],
                        ],
                    ],
                    null,
                    ['HIDE_ICONS' => 'Y']
                );
                break;
            case 'sladcovich_grid_row_commands':
                $APPLICATION->IncludeComponent(
                    'bitrix:system.field.view',
                    'sladcovich_grid_row_commands',
                    [
                        'bVarsFromForm' => [],
                        'arUserField' => [
                            'USER_TYPE' => 'sladcovich_grid_row_commands',
                            'PROPERTY_VALUE_LINK' => '',
                            'VALUE' => [
                                'ID' => $arVars['ID'],
                            ],
                        ],
                    ],
                    null,
                    ['HIDE_ICONS' => 'Y']
                );
                break;
        }
        return ob_get_clean();
    }

    /**
     * Получаем ФИО пользователя
     *
     * @param $userId
     * @return string
     */
    public static function getUserFIO($userId)
    {
        global $USER;
        $rsUser = CUser::GetByID($userId);
        $arUser = $rsUser->Fetch();
        $userFIO = ($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']);

        return $userFIO;
    }

    /* Экшены компонента */
    /**
     * Копируем рабочую смену
     *
     * @param $worksheetId
     * @return mixed
     */
    public function worksheetCopyAction($worksheetId)
    {
        global $USER;

        $res = WorksheetTable::getList([
            'select' => ['*'],
            'filter' => ['ID' => $worksheetId],
            'limit' => 1,
        ]);
        while ($row = $res->fetch()) {
            $current = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime());
            $subRes = WorksheetTable::add([
                'DATETIME_START' => $row['DATETIME_START'],
                'DATETIME_END' => $row['DATETIME_END'],
                'B24_COMPANY_ID' => $row['B24_COMPANY_ID'],
                'B24_MODIFIED_BY_USER_ID' => $USER->GetID(),
                'DATETIME_MODIFY' => $current,
                'SEARCH_USER' => self::getUserFIO($USER->GetID()),
                'SEARCH_COMPANY' => $row['SEARCH_COMPANY'],
                'SEARCH_WORKERS' => $row['SEARCH_WORKERS'],
            ]);
        }

        if ($subRes->isSuccess()) {

            $newWorksheetId = $subRes->getId();

            $arWorkers = [];

            $res = WorkerTable::getList([
                'select' => ['*'],
                'filter' => ['WORKSHEET_ID' => $worksheetId]
            ]);
            while ($row = $res->fetch()) {
                $arWorkers[] = $row;
            }
            foreach ($arWorkers as $worker) {
                $res = WorkerTable::add([
                    'WORKSHEET_ID' => $newWorksheetId,
                    'B24_CONTACT_ID' => intval($worker['B24_CONTACT_ID']),
                    'B24_MODIFIED_BY_USER_ID' => $USER->GetID(),
                    'DATETIME_MODIFY' => $current,
                ]);
            }
        }
    }

    /**
     * Обновляем рабочую смену
     *
     * @param $worksheetId
     * @param $datetimeFrom
     * @param $datetimeTo
     * @param $clientCompany
     * @param $workersContacts
     * @return bool
     * @throws Exception
     */
    public function worksheetUpdateAction($worksheetId ,$datetimeFrom ,$datetimeTo, $clientCompany, $workersContacts)
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

        $res = WorkerTable::getList([
            'select' => ['ID'],
            'filter' => ['WORKSHEET_ID' => $worksheetId]
        ]);
        while ($row = $res->fetch()) {
            WorkerTable::delete($row['ID']);
        }

        $res = WorksheetTable::update(
            $worksheetId,
            [
                'DATETIME_START' => $datetimeFrom,
                'DATETIME_END' => $datetimeTo,
                'B24_COMPANY_ID' => $clientCompany,
                'B24_MODIFIED_BY_USER_ID' => $USER->GetID(),
                'DATETIME_MODIFY' => $current,
                'SEARCH_USER' => $userFIO,
                'SEARCH_COMPANY' => $companyTitle,
                'SEARCH_WORKERS' => $workersFIO
            ]
        );

        if ($res->isSuccess()) {

            foreach ($workersContacts as $worker) {
                $res = WorkerTable::add([
                    'WORKSHEET_ID' => $worksheetId,
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

            return true;
        }

    }

    /**
     * Удаляем рабочую смену
     *
     * @param $worksheetId
     * @return bool
     */
    public function worksheetDeleteAction($worksheetId)
    {
        $res = WorkerTable::getList([
            'select' => ['ID'],
            'filter' => ['WORKSHEET_ID' => $worksheetId]
        ]);
        while ($row = $res->fetch()) {
            WorkerTable::delete($row['ID']);
        }

        $res = WorksheetTable::delete($worksheetId);

        if ($res->isSuccess()) {
            return true;
        }
    }

    /**
     * Получаем данные рабочей смены для popup изменения рабочей смены
     *
     * @param $worksheetId
     * @return array
     */
    public function getWorksheetByIdAction($worksheetId)
    {
        $arWorkersId = [];

        $res = WorkerTable::getList([
            'select' => ['B24_CONTACT_ID'],
            'filter' => ['WORKSHEET_ID' => $worksheetId],
        ]);
        while ($row = $res->fetch()) {
            $arWorkersId[] = $row['B24_CONTACT_ID'];
        }

        $arWorksheet = [];

        $res = WorksheetTable::getList([
            'select' => ['*'],
            'filter' => ['ID' => $worksheetId],
            'limit' => 1,
        ]);

        while ($row = $res->fetch()) {
            $arWorksheet = [
                'DATETIME_START' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($row['DATETIME_START'])),
                'DATETIME_END' => Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($row['DATETIME_END'])),
                'B24_COMPANY_ID' => $row['B24_COMPANY_ID'],
                'WORKERS' => $arWorkersId,
            ];
        }

        return $arWorksheet;
    }
}