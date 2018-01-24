<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use app\modules\admin\widgets\ActionCheckbox;
use app\modules\admin\widgets\ActionInput;
use app\modules\admin\widgets\ActionPanel;
use app\modules\admin\widgets\AddNewItem;

$this->title = 'Content';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= AddNewItem::widget(["link" => Url::to(['content/add'])]) ?>
</div>

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method' => 'GET',
        'action' => Url::to(['content/']),
        'options' => [
            'id' => 'search-form',
        ],
        'fieldConfig' => [
            'template' => "<div class=\"input-field indigo-field\">{input}{label}</div>",
            'labelOptions' => ['class' => '', 'data-error' => 'wrong'],
            'inputOptions' => ['class' => 'validate']
        ],
    ]); ?>

    <div class="row">

        <div class="cell">
            <?= $form->field($search, 'active')->dropDownList([
                "yes" => "Yes",
                "no" => "No",
                "all" => "All",
            ], [
                'class' => 'materialize-select'
            ]) ?>
        </div>

        <div class="cell cell_flex cell_greedy">

            <div class="cell">
            <?= $form->field($search, 'type')->dropDownList([
                "id" => "Id",
                "name" => "Name",
            ], [
                'class' => 'materialize-select',
                'id' => 'search-form__type'
            ]) ?>

            </div>

            <div class="cell cell_greedy cell_cling">

                <div data-type="name">
                    <?= $form->field($search, 'name')->input('text', [
                    ]) ?>
                </div>
                <div data-type="id" style="display: none">
                    <?= $form->field($search, 'id')->input('text', [
                    ]) ?>
                </div>

            </div>

        </div>

    </div>
    <div class="row">

        <div class="cell">
            <?= $form->field($search, 'created_date_begin')->input('text', [
                'class' => 'date_picker'
            ]) ?>
        </div>

        <div class="cell">
            <?= $form->field($search, 'created_date_end')->input('text', [
                'class' => 'date_picker'
            ]) ?>
        </div>

        <div class="cell">
            <?= $form->field($search, 'updated_date_begin')->input('text', [
                'class' => 'date_picker'
            ]) ?>
        </div>

        <div class="cell">
            <?= $form->field($search, 'updated_date_end')->input('text', [
                'class' => 'date_picker'
            ]) ?>
        </div>

    </div>

    <div class="row flex flex_end">
        <?= Html::resetButton('Reset filter', ['class' => 'btn orange', 'style' => 'margin-right: 20px', 'onclick'=>"window.location = '".Url::to(['category/'])."'"]) ?>
        <?= Html::submitButton("Apply filter", ['class' => 'btn btn-primary green waves-effect waves-light']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ActionPanel::widget(["activate" => "/admin/content/activate", "deactivate" => "/admin/content/deactivate", "edit" => "/admin/content/ajax-update"]) ?>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
            <tr>
                <th style="width: 3%">
                    <input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" id="itemall" />
                    <label for="itemall"></label>
                </th>
                <th><?= $sort->link('id', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('name', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('category', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('type', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('price', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('currency', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('rating', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('active', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('created_at', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('updated_at', ["class" => "sort-link"]) ?></th>
                <th style="width: 10%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($content as $item): ?>
                <tr <?= $item->active ? "class=\"active\"":"" ?> data-id="<?= $item->id?>">
                    <td>
                        <input type="checkbox" class="filled-in action-checkbox indigo-field item-checkbox" value="<?=$item->id?>" id="item<?= $item->id?>"/>
                        <label for="item<?= $item->id?>"></label>
                    </td>
                    <td><?= $item->id ?></td>
                    <td><?= ActionInput::widget(["item" => $item, "property" => "name"]) ?></td>
                    <td><?= $item->category->name ?></td>
                    <td><?= $item->contentType->name ?></td>
                    <td><?= ActionInput::widget(["item" => $item, "property" => "price"]) ?></td>
                    <td><?= $item->currencyType->name ?></td>
                    <td><?= ActionInput::widget(["item" => $item, "property" => "rating"]) ?></td>
                    <td><?= ActionCheckbox::widget(["item" => $item, "property" => "active"]) ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $item->created_at) ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $item->updated_at) ?></td>
                    <td style="text-align: right;">
                        <a class="dropdown-button nowrap" href="#!" data-constrainwidth="false" data-activates="dropdown<?= $item->id ?>">Actions<i class="material-icons right">arrow_drop_down</i></a>
                        <ul class="dropdown-content" id="dropdown<?= $item->id ?>">
                            <li>
                                <a href="<?=Url::to(['content/view', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">remove_red_eye</i>View</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['content/update', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">edit</i>Update</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['content/reactivate', 'id' => $item->id]);?>" class="nowrap activate">
                                    <i class="material-icons left">check</i>
                                    <span data-active="Deactivate" data-nonactive="Activate"><?= $item->active ? "Deactivate" : "Activate"?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['log/', 'type' => 'content', 'object_id' => $item->id]);?>" class="nowrap"><i class="material-icons left">history</i>History</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['content/customers', 'category_id' => $item->id]);?>" class="nowrap"><i class="material-icons left">list</i>Customers List</a>
                            </li>
                        </ul>
                    </td>
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
