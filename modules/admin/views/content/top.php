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
                <th>Category</th>
                <th>Type</th>
                <th>Price</th>
                <th>Currency</th>
                <th>Rating</th>
                <th>Active</th>
                <th>Created at</th>
                <th>Update at</th>
                <th>Order</th>
            </tr>
        </thead>
        <tbody class="sortable" data-href="<?=Url::to(['content/top']);?>">
            <?php foreach ($content as $item): ?>
                <tr data-id="<?= $item["id"] ?>">
                    <td class="ordering"></td>
                    <td><?= $item->id ?></td>
                    <td><?= $item->name ?></td>
                    <td><?= $item->category->name ?></td>
                    <td><?= $item->contentType->name ?></td>
                    <td><?= $item->price ?></td>
                    <td><?= $item->currencyType->name ?></td>
                    <td><?= $item->rating ?></td>
                    <td><?= $item->active ? "Yes":"No" ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $item->created_at) ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $item->updated_at) ?></td>
                    <td class="priority"><?= $item->priority ?></td>
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
