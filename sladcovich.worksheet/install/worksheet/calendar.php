<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создать рабочую смену");
?>

<?
$APPLICATION->IncludeComponent('sladcovich:worksheet.calendar','');
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>