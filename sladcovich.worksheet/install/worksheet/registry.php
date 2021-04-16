<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Реестр рабочих смен");
?>

<?
$APPLICATION->IncludeComponent('sladcovich:worksheet.registry','');
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>