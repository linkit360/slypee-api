<?php

use yii\helpers\Html;
use yii\helpers\Url;

use app\modules\admin\widgets\AddNewItem;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['users/'], 'class' => 'breadcrumb'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flex flex_centered flex_between">
    <h1><?= $this->title ?> &mdash; <?= $model->username?></h1>
    <?php
    if (Yii::$app->user->can('createUser')) {
        ?>
        <?= AddNewItem::widget(["label" => "Add new user", "link" => Url::to(['user/add'])]) ?>
        <?php
    }
    ?>
</div>

<table class="striped responsive-table" style="margin: 25px 0 0">
    <tr>
        <td style="width: 15%">Name:</td>
        <td><?= $model->username?></td>
    </tr>
    <tr>
        <td style="width: 15%">Email:</td>
        <td><?= $model->email?></td>
    </tr>
    <tr>
        <td style="width: 15%">Role:</td>
        <td><?= $model->role ?></td>
    </tr>
    <tr>
        <td style="width: 15%">Password:</td>
        <td>******</td>
    </tr>
    <tr>
        <td>Active:</td>
        <td><?= $model->active ? "Yes" : "No"?></td>
    </tr>
</table>

<div class="flex flex_end" style="margin: 25px 0 0">
    <a class="btn orange waves-effect waves-light" href="<?=Url::to(['users/']);?>" style="margin-right: 20px">Back</a>
    <?php
    if (Yii::$app->user->can('updateUser')) {
        ?>
        <a class="btn green waves-effect waves-light" href="<?=Url::to(['users/update', 'id' => $model->id]);?>">Edit</a>
        <?php
    }
    ?>
</div>
