<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\widgets\AddNewItem;

$this->title = 'Content types';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= AddNewItem::widget(["link" => Url::to(['content_types/add'])]) ?>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th>Name</th>
            <th style="width: 20%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($types as $item): ?>
            <tr>
                <td><?= $item->name ?></td>
                <td class="nowrap right-align">
                    <a href="<?=Url::to(['content-types/update', 'id' => $item->id]);?>" class="nowrap icon-link"><i class="material-icons">edit</i>Update</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
