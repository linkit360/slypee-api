<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\widgets\AddNewItem;
use app\modules\admin\widgets\ActionCheckbox;

$this->title = 'Slider';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= AddNewItem::widget(["link" => Url::to(['slider/add'])]) ?>
</div>

<div class="table-actions table-actions_lonely">
    <div class="table-actions__wrapper">
        <div class="table-actions__label">
            Selected items: <strong id="selected_count" class="selected_count">0</strong>
        </div>

        <div class="table-actions__buttons table-actions__buttons_active">
            <?= Html::Button("Activate selected", ['class' => 'btn btn-primary green waves-effect waves-light table-actions_button', 'id' => 'activate-action', 'data-href' => '/admin/slider/activate']) ?>
            <?= Html::Button("Deactivate selected", [
                'class' => 'btn btn-primary orange waves-effect waves-light table-actions_button',
                'id' => 'deactivate-action',
                'data-href' => '/admin/slider/deactivate'
            ])
            ?>
        </div>

    </div>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 3%">
                <input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" id="itemall" />
                <label for="itemall"></label>
            </th>
            <th style="width: 80%">Name</th>
            <th>Active</th>
            <th style="width: 20%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($slider as $item): ?>
            <tr <?= $item->active ? "class=\"active\"":"" ?> data-content="0" data-id="<?= $item->id?>">
                <td>
                    <input type="checkbox" class="filled-in action-checkbox indigo-field item-checkbox" value="<?=$item->id?>" id="item<?= $item->id?>"/>
                    <label for="item<?= $item->id?>"></label>
                </td>
                <td><?= $item->title ?></td>
                <td><?= ActionCheckbox::widget(["item" => $item, "property" => "active"]) ?></td>
                <td style="text-align: right;">
                    <a class="dropdown-button nowrap" href="#!" data-constrainwidth="false" data-activates="dropdown<?= $item->id ?>">Actions<i class="material-icons right">arrow_drop_down</i></a>
                    <ul class="dropdown-content" id="dropdown<?= $item->id ?>">
                        <li>
                            <a href="<?=Url::to(['slider/view', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">remove_red_eye</i>View</a>
                        </li>
                        <li>
                            <a href="<?=Url::to(['slider/update', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">edit</i>Update</a>
                        </li>
                        <li>
                            <a href="<?=Url::to(['slider/activate', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">check</i><?= $item->active ? "Deactivate" : "Activate"?></a>
                        </li>
                        <li>
                            <a href="<?=Url::to(['slider-log/', 'slider_id' => $item->id]);?>" class="nowrap"><i class="material-icons left">history</i>History</a>
                        </li>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
