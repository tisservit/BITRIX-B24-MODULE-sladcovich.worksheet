<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Статистика рабочих смен");
?>

<?
$APPLICATION->IncludeComponent('sladcovich:worksheet.statistic','');
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>