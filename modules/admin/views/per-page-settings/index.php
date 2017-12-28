<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Pagination settings';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th>Section</th>
            <th>Value</th>
            <th style="width: 20%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($settings as $item): ?>
            <tr>
                <td><?= $item->name ?></td>
                <td><?= $item->value ?></td>
                <td class="nowrap right-align">
                    <a href="<?=Url::to(['per-page-settings/update', 'id' => $item->id]);?>" class="nowrap icon-link"><i class="material-icons">edit</i>Update</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
