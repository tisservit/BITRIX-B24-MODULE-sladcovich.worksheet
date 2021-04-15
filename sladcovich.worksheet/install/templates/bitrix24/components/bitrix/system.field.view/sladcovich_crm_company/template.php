<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/*
 *  $arParams['arUserField']['VALUE'] = [
 *      'ID' - id поля, зачастую это id элемента строки в таблице
 *      'COMPANY_ID' - id компании Битрикс 24
 *      'CONTACT_TITLE' - ФИО компании Битрикс 24
 *  ]
 */
?>

<a href="<?= '/crm/company/details/' . $arParams['arUserField']['VALUE']['COMPANY_ID'] . '/'?>">
    <?= $arParams['arUserField']['VALUE']['COMPANY_TITLE'] ?>
</a>
