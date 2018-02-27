<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Content for customer '.$customer->username;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>
</div>


<div class="table-content">
    <table class="striped responsive-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Type</th>
                <th>Price</th>
                <th>Currency</th>
                <th>Rating</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody class="sortable" data-href="<?=Url::to(['content/top']);?>">
            <?php foreach ($content as $item): ?>
                <tr>
                    <td><?= $item->content->id ?></td>
                    <td><?= $item->content->name ?></td>
                    <td><?= $item->content->category->name ?></td>
                    <td><?= $item->content->contentType->name ?></td>
                    <td><?= $item->content->price ?></td>
                    <td><?= $item->content->currencyType->name ?></td>
                    <td><?= $item->content->rating ?></td>
                    <td><?= date("m-d-y H:i", $item->date) ?></td>
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
