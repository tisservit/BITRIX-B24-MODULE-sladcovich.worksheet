<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/*
 *  $arParams['arUserField']['VALUE'] = [
 *      'ID' - id поля, зачастую это id элемента строки в таблице
 *      'WORKERS' - массив контактов в Битрикс 24 ['id контакта' => 'ФИО контакта']
 *  ]
 */
?>

<? foreach ($arParams['arUserField']['VALUE']['WORKERS'] as $workerId => $workerFIO): ?>

    <a href="<?= '/crm/contact/details/' . $workerId . '/' ?>">
        <?= $workerFIO ?>
    </a>
    <br>

<? endforeach; ?>
