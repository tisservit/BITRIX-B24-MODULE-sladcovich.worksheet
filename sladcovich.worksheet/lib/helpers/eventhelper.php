<?php

namespace Sladcovich\Worksheet\Helpers;

use \Bitrix\Main\Loader;

Loader::includeModule('sladcovich.worksheet');
Loader::includeModule('main');

class EventHelper
{
    /**
     * Добавление таба "Календарь рабочих смен"
     *
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function addTabToCompanyCard()
    {
        $engine = new \CComponentEngine();
        $page = $engine->guessComponentPath(
            '/crm/',
            ['detail' => '#entity_type#/details/#entity_id#/'],
            $variables
        );

        // Если страница не является детальной карточкой CRM прервем выполенение
        if ($page !== 'detail') {
            return;
        }

        // Проверим валидность типа сущности
        $allowType = 'company';
        $variables['entity_type'] = strtolower($variables['entity_type']);
        if ($allowType != $variables['entity_type']) {
            return;
        }

        // Проверим валидность идентификатора сущности
        $variables['entity_id'] = (int)$variables['entity_id'];
        if (0 >= $variables['entity_id']) {
            return;
        }

        $assetManager = \Bitrix\Main\Page\Asset::getInstance();

        // Подключаем js файл
        $assetManager->addJs('/bitrix/js/sladcovich.worksheet/crm_company_details.js');

        // Подготовим параметры функции
        $jsParams = \Bitrix\Main\Web\Json::encode(
            $variables,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        // Инициализируем добавление таба
        $assetManager->addString('
        <script>
        BX.ready(function () {
            if (typeof initialize_crm_company_details_tab_sladcovich_worksheet_calendar === "function") {
                initialize_crm_company_details_tab_sladcovich_worksheet_calendar(' . $jsParams . ');
            }
        });
        </script>
    ');
    }
}