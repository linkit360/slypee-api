<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use app\modules\admin\widgets\ActionCheckbox;
use app\modules\admin\widgets\AddNewItem;

$this->title = 'Roles';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= AddNewItem::widget(["link" => Url::to(['roles/add'])]) ?>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 15%">Created At</th>
            <th style="width: 25%">Updated At</th>
            <th style="width: 40%">Name</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($roles as $role): ?>
            <tr>
                <td><?= date("m-d-y", $role->createdAt) ?></td>
                <td><?= date("m-d-y", $role->updatedAt) ?></td>
                <td class="upper"><?= $role->name ?></td>
                <td class="right-align">
                    <?php if(mb_strtolower($role->name) != "admin") { ?>
                        <a href="<?=Url::to(['roles/update', 'name' => $role->name]);?>" class="nowrap icon-link"><i class="material-icons">edit</i>Update</a>
                    <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>