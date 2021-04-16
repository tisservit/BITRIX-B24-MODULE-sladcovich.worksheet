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

?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-dismissable alert-info">
                <?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_PLANING_1'); ?><?= Date('d.m.Y') ?><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_PLANING_2'); ?><?= $arResult['COMMON']['TOTAL'] ?><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_PLANING_3'); ?>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6">
            <div id="card-no-worksheets">

                <div class="card">
                    <div class="card-header">
                        <a class="card-link collapsed sladcovich-worksheet__text-green" data-toggle="collapse" data-parent="#card-no-worksheets"
                           href="#card-with-worksheets-customers"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_CUSTOMERS_WITH_WORKSHEET') ?>
                            - <?= $arResult['CUSTOMERS']['WORKSHEET_YES']['TOTAL'] ?></a>
                    </div>
                    <div id="card-with-worksheets-customers" class="collapse">
                        <div class="card-body">

                            <table class="table">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_CUSTOMERS') ?></th>
                                </tr>
                                </thead>
                                <tbody>

                                <? foreach ($arResult['CUSTOMERS']['WORKSHEET_YES']['DATA'] as $id => $name): ?>

                                    <tr>
                                        <td>
                                            <a href="/crm/company/details/<?= $id ?>/"><?= $name ?></a>
                                        </td>
                                    </tr>

                                <? endforeach; ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header">
                        <a class="card-link collapsed sladcovich-worksheet__text-green" data-toggle="collapse" data-parent="#card-no-worksheets"
                           href="#card-with-worksheets-workers"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_WORKERS_WITH_WORKSHEET') ?>
                            - <?= $arResult['WORKERS']['WORKSHEET_YES']['TOTAL'] ?></a>
                    </div>
                    <div id="card-with-worksheets-workers" class="collapse">
                        <div class="card-body">

                            <table class="table">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_WORKERS') ?></th>
                                </tr>
                                </thead>
                                <tbody>

                                <? foreach ($arResult['WORKERS']['WORKSHEET_YES']['DATA'] as $id => $name): ?>

                                    <tr>
                                        <td>
                                            <a href="/crm/contact/details/<?= $id ?>/"><?= $name ?></a>
                                        </td>
                                    </tr>

                                <? endforeach; ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div id="card-with-worksheets">

                <div class="card">
                    <div class="card-header">
                        <a class="card-link collapsed sladcovich-worksheet__text-red" data-toggle="collapse" data-parent="#card-with-worksheets"
                           href="#card-no-worksheets-customers"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_CUSTOMERS_NO_WORKSHEET') ?>
                            - <?= $arResult['CUSTOMERS']['WORKSHEET_NO']['TOTAL'] ?></a>
                    </div>
                    <div id="card-no-worksheets-customers" class="collapse">
                        <div class="card-body">

                            <table class="table">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_CUSTOMERS') ?></th>
                                </tr>
                                </thead>
                                <tbody>

                                <? foreach ($arResult['CUSTOMERS']['WORKSHEET_NO']['DATA'] as $id => $name): ?>

                                    <tr>
                                        <td>
                                            <a href="/crm/company/details/<?= $id ?>/"><?= $name ?></a>
                                        </td>
                                    </tr>

                                <? endforeach; ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header">
                        <a class="collapsed card-link sladcovich-worksheet__text-red" data-toggle="collapse" data-parent="#card-with-worksheets"
                           href="#card-no-worksheets-workers"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_WORKERS_NO_WORKSHEET') ?>
                            - <?= $arResult['WORKERS']['WORKSHEET_NO']['TOTAL'] ?></a>
                    </div>
                    <div id="card-no-worksheets-workers" class="collapse">
                        <div class="card-body">

                            <table class="table">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col"><?= GetMessage('SLADCOVICH_WORKSHEET_STATISTIC_WORKERS') ?></th>
                                </tr>
                                </thead>
                                <tbody>

                                <? foreach ($arResult['WORKERS']['WORKSHEET_NO']['DATA'] as $id => $name): ?>

                                    <tr>
                                        <td>
                                            <a href="/crm/contact/details/<?= $id ?>/"><?= $name ?></a>
                                        </td>
                                    </tr>

                                <? endforeach; ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
