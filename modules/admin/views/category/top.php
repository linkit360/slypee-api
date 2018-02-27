<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Top chart';
$this->params['breadcrumbs'][] = $this->title;
$per_page_values = [
    "10" => 10,
    "50" => 50,
    "100" => 100,
    "1000" => 1000,
    "65000" => "All"
];
?>

<div class="flex flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="per-page-action">
        <div style="position: relative; transform: translate(0, -10px)">
            <span style="position: absolute; right: 100%; white-space: nowrap; margin-top: 10px; margin-right: 10px">Items per page:</span>
            <select class="materialize-select" id="per_page_settings" style="width: 150px" data-type="category" data-url="/admin/category/top">
                <?php
                foreach ($per_page_values as $index => $value) {
                    ?>
                    <option value="<?=$index?>"<?=$session_per_page_settings == $index ? "selected=\"selected\"":""?>><?=$value?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
            <tr>
                <th style="width: 35px">&nbsp;</th>
                <th>Order</th>
                <th>Name</th>
                <th>Active</th>
                <th>Main menu</th>
                <th>Main page</th>
                <th>Created at</th>
                <th>Update at</th>
                <th>Content</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody class="sortable" data-href="<?=Url::to(['category/top']);?>">
            <?php foreach ($categories as $category): ?>
                <tr data-id="<?= $category["id"] ?>">
                    <td class="ordering"></td>
                    <td class="priority"><?= $category->priority ?></td>
                    <td><?= $category->name ?></td>
                    <td><?= $category->active ? "Yes":"No" ?></td>
                    <td><?= $category->main_menu ? "Yes":"No" ?></td>
                    <td><?= $category->main_page ? "Yes":"No" ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $category->created_at) ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $category->updated_at) ?></td>
                    <td><?= $category->content ?></td>
                    <td><?= $category->id ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
        echo LinkPager::widget([
            'pagination' => $pages,
            'prevPageLabel' => '<i class="material-icons">chevron_left</i>',
            'nextPageLabel' => '<i class="material-icons">chevron_right</i>',
        ]);
    ?>
</div>
