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

?>

<div class="container-fluid">
    <form id="sladcovich-worksheet__form">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-header">
                        <?= GetMessage("SLADCOVICH_WORKSHEET_CREATE_TITLE") ?>
                    </h5>
                    <div class="card-body">

                        <div class="container-fluid">
                            <div class="row pt-3">
                                <div class="col-2"><?= GetMessage("SLADCOVICH_WORKSHEET_CREATE_DATETIME_FROM") ?></div>
                                <div class="col-2">
                                    <input
                                            name="sladcovich-worksheet__datetime_from"
                                            id="sladcovich-worksheet__datetime_from"
                                            type="datetime-local"
                                            class="form-control sladcovich-worksheet__input_datetime"
                                            required
                                    />
                                </div>
                                <div class="col-1"><?= GetMessage("SLADCOVICH_WORKSHEET_CREATE_CUSTOMER") ?></div>
                                <div class="col-7">
                                    <select
                                            name="sladcovich-worksheet__js_select2_company[]"
                                            class="sladcovich-worksheet__js_select2"
                                            id="sladcovich-worksheet__js_select2_company"
                                            required>
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-3">
                                <div class="col-2"><?= GetMessage("SLADCOVICH_WORKSHEET_CREATE_DATETIME_TO") ?></div>
                                <div class="col-2">
                                    <input
                                            name="sladcovich-worksheet__datetime_to"
                                            id="sladcovich-worksheet__datetime_to"
                                            type="datetime-local"
                                            class="form-control sladcovich-worksheet__input_datetime"
                                            required
                                    />
                                </div>
                                <div class="col-1"><?= GetMessage("SLADCOVICH_WORKSHEET_CREATE_EMPLOYEES") ?></div>
                                <div class="col-7">
                                    <select
                                            name="sladcovich-worksheet__js_select2_contact[]"
                                            multiple="multiple"
                                            class="sladcovich-worksheet__js_select2"
                                            id="sladcovich-worksheet__js_select2_contact"
                                            required>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="ui-btn ui-btn-success ui-btn-lg">
                            <?= GetMessage("SLADCOVICH_WORKSHEET_CREATE_ADD_WORKSHEET") ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="container-fluid mt-3" id="sladcovich-worksheet__js_info_panel"></div>

<script>
    $(document).ready(function () {

        // Добавляем компании в select2
        $('#sladcovich-worksheet__js_select2_company').select2({
            data: <?=\Bitrix\Main\Web\Json::encode($arResult['CUSTOMERS_COMPANIES']);?>,
            language: {
                noResults: function () {
                    return '<?=GetMessage('SLADCOVICH_WORKSHEET_CREATE_CUSTOMER_NOT_FOUND')?>';
                }
            },
            placeholder: '<?=GetMessage('SLADCOVICH_WORKSHEET_CREATE_CUSTOMER_PLACEHOLDER')?>',
            allowClear: true
        });

        // Добавляем контакты в select2
        $('#sladcovich-worksheet__js_select2_contact').select2({
            data: <?=\Bitrix\Main\Web\Json::encode($arResult['WORKERS_CONTACTS']);?>,
            language: {
                noResults: function () {
                    return '<?=GetMessage('SLADCOVICH_WORKSHEET_CREATE_EMPLOYEE_NOT_FOUND')?>';
                }
            },
            placeholder: '<?=GetMessage('SLADCOVICH_WORKSHEET_CREATE_EMPLOYEE_PLACEHOLDER')?>',
            allowClear: true
        });

        // Удаляем первый option в select2 (non multiple)
        $('#sladcovich-worksheet__js_select2_company').on('change', function () {
            //console.log($('[value="delete"]'));
            $('[value="delete"]').remove();
        });

        // Добавляем рабочую смену
        $('#sladcovich-worksheet__form').on('submit', function (e) {
            e.preventDefault();

            let datetimeFrom = $('#sladcovich-worksheet__datetime_from').val();
            let datetimeTo = $('#sladcovich-worksheet__datetime_to').val();
            let clientCompany = $('#sladcovich-worksheet__js_select2_company').val();
            let workersContacts = $('#sladcovich-worksheet__js_select2_contact').val();

            BX.ajax.runComponentAction('sladcovich:worksheet.create', 'createNewWorksheet', {
                mode: 'class', // это означает, что мы хотим вызывать действие из class.php
                data: {
                    datetimeFrom: datetimeFrom,
                    datetimeTo: datetimeTo,
                    clientCompany: clientCompany,
                    workersContacts: workersContacts
                },
            }).then(function (response) {
                // success
                let workersFIO = '';
                response.data.WORKERS_FIO.forEach(function (item) {
                    workersFIO = workersFIO + item + ', '
                });
                $('#sladcovich-worksheet__js_info_panel').append(
                    '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '<strong><?=GetMessage("SLADCOVICH_WORKSHEET_CREATE_WORKSHEET_ADDED")?></strong>' + '' +
                    '<?=GetMessage("SLADCOVICH_WORKSHEET_CREATE_WORKSHEET_ADDED_FOR_CUSTOMER")?>' +
                    response.data.COMPANY_TITLE + ' (' + response.data.DATETIME_FROM + ' - ' + response.data.DATETIME_TO + ')' +
                    '<?=GetMessage("SLADCOVICH_WORKSHEET_CREATE_WORKSHEET_WORKERS_FIO")?>' + workersFIO.slice(0, -2) +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>'
                );
            }, function (response) {
                // error
                $('#sladcovich-worksheet__js_info_panel').append(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<strong><?=GetMessage("SLADCOVICH_WORKSHEET_CREATE_WORKSHEET_ADDED")?></strong>' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>'
                );
                console.log('SLADCOVICH - START');
                console.log(response);
                console.log('SLADCOVICH - END');
            });

            // Сброс формы
            $('#sladcovich-worksheet__form')[0].reset();
            $('#sladcovich-worksheet__js_select2_company').val(null).trigger('change');
            $('#sladcovich-worksheet__js_select2_contact').val(null).trigger('change');

        });


    });
</script>