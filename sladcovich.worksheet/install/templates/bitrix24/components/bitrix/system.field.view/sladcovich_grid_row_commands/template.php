<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/*
 *  $arParams['arUserField']['VALUE'] = [
 *      'ID' - id поля, зачастую это id элемента строки в таблице
 *  ]
 */
?>

<?
Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/css/main/font-awesome.css');
$id = $arParams['arUserField']['VALUE']['ID'];
?>

<button type="button" class="btn btn-outline-secondary sladcovich-worksheet_command-button mt-1" id="<?= $id ?>"
        data-role="entity-copy">
    <i class="fa fa-copy" style="font-size:18px"></i>
</button>

<button type="button" class="btn btn-outline-secondary sladcovich-worksheet_command-button mt-1" id="<?= $id ?>"
        data-role="entity-edit">
    <i class="fa fa-edit" style="font-size:18px"></i>
</button>

<button type="button" class="btn btn-outline-secondary sladcovich-worksheet_command-button mt-1" id="<?= $id ?>"
        data-role="entity-delete">
    <i class="fa fa-remove" style="font-size:18px"></i>
</button>
