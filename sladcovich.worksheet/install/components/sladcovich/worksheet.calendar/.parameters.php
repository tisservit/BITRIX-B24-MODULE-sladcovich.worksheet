<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();



$arComponentParameters = [
    'PARAMETERS' => [
        'COMPANY_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => GetMessage('SLADCOVICH_WORKSHEET_CALENDAR_PARAMETER_COMPANY_ID'),
            'TYPE' => 'NUMBER',
            'DEFAULT' => "",
        )
    ]
];