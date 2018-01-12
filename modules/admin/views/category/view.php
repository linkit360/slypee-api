<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['category/'], 'class' => 'breadcrumb'];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?> &mdash; <?= $model->name?></h1>

<table class="striped responsive-table" style="margin: 25px 0 0">
    <tr>
        <td style="width: 15%">Name:</td>
        <td><?= $model->name?></td>
    </tr>
    <tr>
        <td>Description:</td>
        <td><?= $model->description ?></td>
    </tr>
    <tr>
        <td>Main page:</td>
        <td><?= $model->main_page ? "Yes" : "No"?></td>
    </tr>
    <tr>
        <td>Main menu:</td>
        <td><?= $model->main_menu ? "Yes" : "No"?></td>
    </tr>
    <tr>
        <td>Active:</td>
        <td><?= $model->active ? "Yes" : "No"?></td>
    </tr>
</table>

<div class="flex flex_end" style="margin: 25px 0 0">
    <a class="btn orange waves-effect waves-light" href="<?=Url::to(['category/']);?>" style="margin-right: 20px">Back</a>
    <a class="btn green waves-effect waves-light" href="<?=Url::to(['category/update', 'id' => $model->id]);?>">Edit</a>
</div>
