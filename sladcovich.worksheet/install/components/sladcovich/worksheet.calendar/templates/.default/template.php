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

# fullcalendar
Bitrix\Main\Page\Asset::getInstance()->addCss('/local/dist/sladcovich/fullcalendar/css/main.min.css');
Bitrix\Main\Page\Asset::getInstance()->addJs('/local/dist/sladcovich/fullcalendar/js/main.min.js');

# tippy
Bitrix\Main\Page\Asset::getInstance()->addJs('/local/dist/sladcovich/tippy/js/popper.min.js');
Bitrix\Main\Page\Asset::getInstance()->addJs('/local/dist/sladcovich/tippy/js/tippy.min.js');

?>

<? if ($arResult['CUSTOMER_MODE'] === true): ?>

    <script>
        $(document).ready(function () {

            // Создание календаря
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                events: <?=$arResult['JSON']['EVENTS'];?>,
                initialView: 'dayGridMonth',
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                locale: 'ru',
                dragScroll: false,
                aspectRatio: 3,
                weekNumberCalculation: 'ISO',
                eventClick: function (info) {
                    // info.event - объект события
                },
                eventDidMount: function (info) {
                    // Массив работников
                    // info.event.extendedProps.workers
                    let content = '' +
                        '<div class="container-fluid">' +
                        '<div class="row">' +
                        '<div class="col-12">' +
                        '<table class="table table-striped table-dark">' +
                        '<thead>' +
                        '<tr>' +
                        '<th>' +
                        '№' +
                        '</th>' +
                        '<th style="color: #fffff">' +
                        '<?=GetMessage("SLADCOVICH_WORKSHEET_CALENDAR_WORKER")?>' +
                        '</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody>' +
                        '';

                    info.event.extendedProps.workers.forEach(function (item, index) {
                        content = content + '' +
                            '<tr>' +
                            '<td>' +
                            (index + 1) +
                            '</td>' +
                            '<td>' +
                            item +
                            '</td>' +
                            '</tr>' +
                            ''
                    });

                    content = content + '' +
                        '<thead>' +
                        '<tr>' +
                        '<th>' +
                        '<?=GetMessage("SLADCOVICH_WORKSHEET_CALENDAR_START")?>' +
                        '</th>' +
                        '<th>' +
                        info.event.start.toLocaleString() +
                        '</th>' +
                        '</tr>' +
                        '<tr>' +
                        '<th>' +
                        '<?=GetMessage("SLADCOVICH_WORKSHEET_CALENDAR_END")?>' +
                        '</th>' +
                        '<th>' +
                        info.event.end.toLocaleString() +
                        '</th>' +
                        '</tr>' +
                        '</thead>' +
                        ''

                    content = content + '' +
                        '</table>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '';

                    tippy(info.el, {
                        content: content,
                        arrow: false,
                        allowHTML: true
                    });
                }
            });
            calendar.render();

            // Замена кнопки "today" на "Сегодня"
            $('.fc-today-button.fc-button.fc-button-primary').text('Сегодня');
            $('.fc-button-primary').on('click', function () {
                $('.fc-today-button.fc-button.fc-button-primary').text('Сегодня');
            });

            // Перерендеринг календаря - для загрузки календаря с таба сущностей
            if (<?=$arResult['COMPANY_ID']?> > 0) {
                function reloadCalendar() {
                    $('.fc-today-button').attr('disabled', false);
                    $('.fc-today-button').trigger('click');
                    $('.fc-today-button').attr('disabled', true);
                }

                setTimeout(reloadCalendar, 100);
            }

        });
    </script>

    <div class="container-fluid mb-5 mt-5">
        <div id='calendar'></div>
    </div>

<? else: ?>

    <div class="container-fluid mb-5 mt-5">
        <div class="alert alert-warning" role="alert">
            <?= GetMessage('SLADCOVICH_WORKSHEET_CALENDAR_IT_IS_NO_CUSTOMER') ?>
        </div>
    </div>

<? endif; ?>
