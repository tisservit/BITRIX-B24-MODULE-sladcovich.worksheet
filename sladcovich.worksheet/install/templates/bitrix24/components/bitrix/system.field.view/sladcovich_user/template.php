<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/*
 *  $arParams['arUserField']['VALUE'] = [
 *      'ID' - id поля, зачастую это id элемента строки в таблице
 *      'USER_ID' - id пользователя Битрикс 24
 *      'USER_FIO' - ФИО пользователя Битрикс 24
 *  ]
 */
?>

<a href="<?= '/company/personal/user/' . $arParams['arUserField']['VALUE']['USER_ID'] . '/'?>">
    <?= $arParams['arUserField']['VALUE']['USER_FIO'] ?>
</a>
