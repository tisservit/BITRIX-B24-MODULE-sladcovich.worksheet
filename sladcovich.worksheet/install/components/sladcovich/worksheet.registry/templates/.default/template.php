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

\Bitrix\Main\UI\Extension::load("ui.bootstrap4");

# select 2
Bitrix\Main\Page\Asset::getInstance()->addCss('/local/dist/sladcovich/select2/css/select2.min.css');
Bitrix\Main\Page\Asset::getInstance()->addJs('/local/dist/sladcovich/select2/js/select2.min.js');

CJSCore::Init(['popup']);

?>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <?
            $APPLICATION->IncludeComponent(
                'bitrix:main.ui.filter',
                $arResult['FILTER']['TEMPLATE'],
                [
                    'FILTER_ID' => $arResult['COMMON']['ID'],
                    'GRID_ID' => $arResult['COMMON']['ID'],
                    'FILTER' => $arResult['FILTER']['COLUMNS'],
                    'FILTER_PRESETS' => [],
                    'ENABLE_LIVE_SEARCH' => false,
                    'ENABLE_LABEL' => true,
                ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?
            $APPLICATION->IncludeComponent(
                'bitrix:main.ui.grid',
                $arResult['GRID']['TEMPLATE'],
                [
                    'GRID_ID' => $arResult['COMMON']['ID'],
                    'COLUMNS' => $arResult['GRID']['COLUMNS'],
                    'ROWS' => $arResult['GRID']['DATA'],
                    'SHOW_ROW_CHECKBOXES' => false,
                    'NAV_OBJECT' => $arResult['GRID']['NAV_OBJECT'],
                    'TOTAL_ROWS_COUNT' => $arResult['GRID']['TOTAL_ROWS_COUNT'],
                    'AJAX_MODE' => 'Y',
                    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', $arResult['GRID']['TEMPLATE'], ''),
                    'PAGE_SIZES' => [
                        ['NAME' => '5', 'VALUE' => '5'],
                        ['NAME' => '10', 'VALUE' => '10'],
                        ['NAME' => '20', 'VALUE' => '20'],
                        ['NAME' => '50', 'VALUE' => '50'],
                        ['NAME' => '100', 'VALUE' => '100']
                    ],
                    'AJAX_OPTION_JUMP' => 'N',
                    'SHOW_CHECK_ALL_CHECKBOXES' => false,
                    'SHOW_ROW_ACTIONS_MENU' => false,
                    'SHOW_GRID_SETTINGS_MENU' => true,
                    'SHOW_NAVIGATION_PANEL' => true,
                    'SHOW_PAGINATION' => true,
                    'SHOW_SELECTED_COUNTER' => false,
                    'SHOW_TOTAL_COUNTER' => true,
                    'SHOW_PAGESIZE' => true,
                    'SHOW_ACTION_PANEL' => false,
                    'ACTION_PANEL' => [],
                    'ALLOW_COLUMNS_SORT' => true,
                    'ALLOW_COLUMN_RESIZE' => true,
                    'ALLOW_HORIZONTAL_SCROLL' => true,
                    'ALLOW_SORT' => true,
                    'ALLOW_PIN_HEADER' => true,
                    'AJAX_OPTION_HISTORY' => 'N',
                ]);
            ?>
        </div>
    </div>

</div>

<script>
    $(document).ready(function () {

        let reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
        let gridObject = BX.Main.gridManager.getById('<?=$arResult['COMMON']['ID']?>');
        let worksheetId = 0;

        // Копирование рабочей смены
        $(document).on('click', 'button[data-role="entity-copy"]', function (e) {
            worksheetId = $(this).attr('id');
            BX.ajax.runComponentAction('sladcovich:worksheet.registry', 'worksheetCopy', {
                mode: 'class', // это означает, что мы хотим вызывать действие из class.php
                data: {
                    worksheetId: worksheetId
                },
            }).then(function (response) {
                // success
                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', reloadParams);
                }
            }, function (response) {
                // error
                console.log('SLADCOVICH - START');
                console.log(response);
                console.log('SLADCOVICH - END');
            });
        })

        // Редактирование рабочей смены
        $(document).on('click', 'button[data-role="entity-edit"]', function (e) {
            worksheetId = $(this).attr('id');

            BX.ajax.runComponentAction('sladcovich:worksheet.registry', 'getWorksheetById', {
                mode: 'class', // это означает, что мы хотим вызывать действие из class.php
                data: {
                    worksheetId: worksheetId
                },
            }).then(function (response) {
                // success
                let b24Company = response.data.B24_COMPANY_ID;
                let datetimeFrom = response.data.DATETIME_START;
                let datetimeTo = response.data.DATETIME_END;
                let workers = response.data.WORKERS

                console.log('SLADCOVICH - ERROR - START');
                console.log(b24Company);
                console.log(datetimeFrom);
                console.log(datetimeTo);
                console.log(workers);
                console.log('SLADCOVICH - ERROR - END');

            }, function (response) {
                // error
                console.log('SLADCOVICH - START');
                console.log(response);
                console.log('SLADCOVICH - END');
            });

            let popupContent = '' +
                '<div class="container-fluid">' +
                '<form id="sladcovich-worksheet__form_popup">' +
                '<div class="row">' +
                '<div class="col-md-12">' +
                '<div class="card">' +
                '<h5 class="card-header"><?= GetMessage("SLADCOVICH_WORKSHEET_REGISTRY_POPUP_EDIT_TITLE") ?></h5>' +
                '<div class="card-body">' +
                '<div class="container-fluid">' +
                '<div class="row pt-3">' +
                '<div class="col-2"><?= GetMessage("SLADCOVICH_WORKSHEET_REGISTRY_POPUP_DATETIME_FROM") ?></div>' +
                '<div class="col-2">' +
                '<input ' +
                'id="sladcovich-worksheet__datetime_from" ' +
                'type="datetime-local" ' +
                'class="form-control sladcovich-worksheet__input_datetime" ' +
                'value="'+datetimeFrom+'"' +
                'required ' +
                '/>' +
                '</div>' +
                '<div class="col-1"><?= GetMessage("SLADCOVICH_WORKSHEET_REGISTRY_POPUP_CUSTOMER") ?></div>' +
                '<div class="col-7">' +
                '<select ' +
                'name="sladcovich-worksheet__js_select2_company[]" ' +
                'class="sladcovich-worksheet__js_select2" ' +
                'id="sladcovich-worksheet__js_select2_company" ' +
                'required>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="row pt-3">' +
                '<div class="col-2"><?= GetMessage("SLADCOVICH_WORKSHEET_REGISTRY_POPUP_DATETIME_TO") ?></div>' +
                '<div class="col-2">' +
                '<input ' +
                'name="sladcovich-worksheet__datetime_to" ' +
                'id="sladcovich-worksheet__datetime_to" ' +
                'type="datetime-local" ' +
                'class="form-control sladcovich-worksheet__input_datetime" ' +
                'value="'+datetimeTo+'"' +
                'required' +
                '/>' +
                '</div>' +
                '<div class="col-1"><?= GetMessage("SLADCOVICH_WORKSHEET_REGISTRY_POPUP_EMPLOYEES") ?></div>' +
                '<div class="col-7">' +
                '<select ' +
                'name="sladcovich-worksheet__js_select2_contact[]" ' +
                'multiple="multiple" ' +
                'class="sladcovich-worksheet__js_select2" ' +
                'id="sladcovich-worksheet__js_select2_contact" ' +
                'required>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="card-footer">' +
                '<button type="submit" class="ui-btn ui-btn-success ui-btn-lg">' +
                '<?= GetMessage("SLADCOVICH_WORKSHEET_REGISTRY_POPUP_CHANGE") ?>' +
                '</button>' +
                '<span class="ui-btn ui-btn-primary ui-btn-lg">' +
                '<?= GetMessage("SLADCOVICH_WORKSHEET_REGISTRY_POPUP_CANCEL") ?>' +
                '</span>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</form>' +
                '</div>' +
                '';

            let popup = BX.PopupWindowManager.create("popup-message", BX('element'), {
                content: popupContent,
                width: 1600, // ширина окна
                height: 260, // высота окна
                zIndex: 100, // z-index
                closeIcon: {
                    // объект со стилями для иконки закрытия, при null - иконки не будет
                    opacity: 1
                },
                closeByEsc: true, // закрытие окна по esc
                darkMode: false, // окно будет светлым или темным
                autoHide: false, // закрытие при клике вне окна
                draggable: true, // можно двигать или нет
                resizable: false, // можно ресайзить
                lightShadow: true, // использовать светлую тень у окна
                angle: false, // появится уголок
                overlay: {
                    // объект со стилями фона
                    backgroundColor: 'black',
                    opacity: 500
                },
                // buttons: [
                //     new BX.PopupWindowButton({
                //         text: 'Сохранить', // текст кнопки
                //         id: 'sladcovich-worksheet__popup-save', // идентификатор
                //         className: 'ui-btn ui-btn-success', // доп. классы
                //         events: {
                //             click: function () {
                //                 // Событие при клике на кнопку
                //                 // Сохранить исполнителей к работе, а также добавить кол-во исполнителей в кнопке
                //                 // в главной таблицу
                //
                //             }
                //         }
                //     }),
                //     new BX.PopupWindowButton({
                //         text: 'ОК', // текст кнопки
                //         id: 'sladcovich-worksheet__popup-cancel',
                //         className: 'ui-btn ui-btn-primary',
                //         events: {
                //             click: function () {
                //                 // Событие при клике на кнопку
                //                 popup.destroy();
                //             }
                //         }
                //     })
                // ],
                events: {
                    onPopupShow: function () {
                        // Событие при показе окна
                    },
                    onPopupClose: function () {
                        // Событие при закрытии окна
                        popup.destroy();
                    }
                }
            });

            popup.show();

            // Добавляем компании в select2
            $('#sladcovich-worksheet__js_select2_company').select2({
                data: <?=\Bitrix\Main\Web\Json::encode($arResult['COMMON']['CUSTOMERS_COMPANIES']);?>,
                language: {
                    noResults: function () {
                        return '<?=GetMessage('SLADCOVICH_WORKSHEET_REGISTRY_POPUP_CUSTOMER_NOT_FOUND')?>';
                    }
                },
                placeholder: '<?=GetMessage('SLADCOVICH_WORKSHEET_REGISTRY_POPUP_CUSTOMER_PLACEHOLDER')?>',
                allowClear: true
            });

            // Добавляем контакты в select2
            $('#sladcovich-worksheet__js_select2_contact').select2({
                data: <?=\Bitrix\Main\Web\Json::encode($arResult['COMMON']['WORKERS_CONTACTS']);?>,
                language: {
                    noResults: function () {
                        return '<?=GetMessage('SLADCOVICH_WORKSHEET_REGISTRY_POPUP_EMPLOYEE_NOT_FOUND')?>';
                    }
                },
                placeholder: '<?=GetMessage('SLADCOVICH_WORKSHEET_REGISTRY_POPUP_EMPLOYEE_PLACEHOLDER')?>',
                allowClear: true
            });

        });

        // Удаление рабочей смены
        $(document).on('click', 'button[data-role="entity-delete"]', function (e) {
            worksheetId = $(this).attr('id');
            BX.ajax.runComponentAction('sladcovich:worksheet.registry', 'worksheetDelete', {
                mode: 'class', // это означает, что мы хотим вызывать действие из class.php
                data: {
                    worksheetId: worksheetId
                },
            }).then(function (response) {
                // success
                if (gridObject.hasOwnProperty('instance')) {
                    gridObject.instance.reloadTable('POST', reloadParams);
                }
            }, function (response) {
                // error
                console.log('SLADCOVICH - START');
                console.log(response);
                console.log('SLADCOVICH - END');
            });
        });

    });
</script>