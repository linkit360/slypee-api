<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Slider', 'url' => ['slider/'], 'class' => 'breadcrumb'];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?> &mdash; <?= $model->title?></h1>

<table class="striped responsive-table" style="margin: 25px 0 0">
    <tr>
        <td style="width: 15%">Title:</td>
        <td><?= $model->title?></td>
    </tr>
    <tr>
        <td style="width: 15%">Subtitle:</td>
        <td><?= $model->subtitle?></td>
    </tr>
    <tr>
        <td>Description:</td>
        <td><?= $model->description ?></td>
    </tr>
    <tr>
        <td>Link:</td>
        <td><?= $model->link ?></td>
    </tr>
    <tr>
        <td>Image:</td>
        <td>
            <?php
            if($model->image) {
                ?>
                <img src="/<?= $model->uploadPath.$model->image ?>" alt="" style="max-width: 100%" />
                <?php
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>Active:</td>
        <td><?= $model->active ? "Yes" : "No"?></td>
    </tr>
</table>

<div class="flex flex_end" style="margin: 25px 0 0">
    <a class="btn orange waves-effect waves-light" href="<?=Url::to(['slider/']);?>" style="margin-right: 20px">Back</a>
    <a class="btn green waves-effect waves-light" href="<?=Url::to(['slider/update', 'id' => $model->id]);?>">Edit</a>
</div>
