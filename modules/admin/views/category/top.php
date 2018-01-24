<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Top chart';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
            <tr>
                <th style="width: 35px">&nbsp;</th>
                <th>ID</th>
                <th>Name</th>
                <th>Active</th>
                <th>Main menu</th>
                <th>Main page</th>
                <th>Created at</th>
                <th>Update at</th>
                <th>Content</th>
                <th>Order</th>
            </tr>
        </thead>
        <tbody class="sortable" data-href="<?=Url::to(['category/top']);?>">
            <?php foreach ($categories as $category): ?>
                <tr data-id="<?= $category["id"] ?>">
                    <td class="ordering"></td>
                    <td><?= $category->id ?></td>
                    <td><?= $category->name ?></td>
                    <td><?= $category->active ? "Yes":"No" ?></td>
                    <td><?= $category->main_menu ? "Yes":"No" ?></td>
                    <td><?= $category->main_page ? "Yes":"No" ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $category->created_at) ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $category->updated_at) ?></td>
                    <td><?= $category->content ?></td>
                    <td class="priority"><?= $category->priority ?></td>
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
