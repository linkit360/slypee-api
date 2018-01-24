<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use app\modules\admin\widgets\ActionCheckbox;
use app\modules\admin\widgets\ActionInput;
use app\modules\admin\widgets\ActionPanel;
use app\modules\admin\widgets\AddNewItem;

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= AddNewItem::widget(["link" => Url::to(['category/add'])]) ?>
</div>

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method' => 'GET',
        'action' => Url::to(['category/']),
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

<?= ActionPanel::widget(["activate" => "/admin/category/activate", "deactivate" => "/admin/category/deactivate", "edit" => "/admin/category/ajax-update"]) ?>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
            <tr>
                <th style="width: 3%">
                    <input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" id="itemall" />
                    <label for="itemall"></label>
                </th>
                <th><?= $sort->link('id', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('name', ["class" => "sort-link"]) ?>
                <th><?= $sort->link('active', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('main_menu', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('main_page', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('created_at', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('updated_at', ["class" => "sort-link"]) ?></th>
                <th><?= $sort->link('content', ["class" => "sort-link"]) ?></th>
                <th style="width: 10%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr <?= $category->active ? "class=\"active\"":"" ?> data-content="<?= $category->content ?>" data-id="<?= $category->id?>">
                    <td>
                        <input type="checkbox" class="filled-in action-checkbox indigo-field item-checkbox" value="<?=$category->id?>" id="item<?= $category->id?>"/>
                        <label for="item<?= $category->id?>"></label>
                    </td>
                    <td><?= $category->id ?></td>
                    <td><?= ActionInput::widget(["item" => $category, "property" => "name"]) ?></td>
                    <td><?= ActionCheckbox::widget(["item" => $category, "property" => "active"]) ?></td>
                    <td><?= ActionCheckbox::widget(["item" => $category, "property" => "main_menu"]) ?></td>
                    <td><?= ActionCheckbox::widget(["item" => $category, "property" => "main_page"]) ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $category->created_at) ?></td>
                    <td class="nowrap grey-text darken-1"><?= date("m-d-y", $category->updated_at) ?></td>
                    <td><?= $category->content ?></td>
                    <td style="text-align: right;">
                        <a class="dropdown-button nowrap" href="#!" data-constrainwidth="false" data-activates="dropdown<?= $category->id ?>">Actions<i class="material-icons right">arrow_drop_down</i></a>
                        <ul class="dropdown-content" id="dropdown<?= $category->id ?>">
                            <li>
                                <a href="<?=Url::to(['category/view', 'id' => $category->id]);?>" class="nowrap"><i class="material-icons left">remove_red_eye</i>View</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['category/update', 'id' => $category->id]);?>" class="nowrap"><i class="material-icons left">edit</i>Update</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['category/reactivate', 'id' => $category->id]);?>" class="nowrap activate">
                                    <i class="material-icons left">check</i>
                                    <span data-active="Deactivate" data-nonactive="Activate"><?= $category->active ? "Deactivate" : "Activate"?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['log/', 'type' => 'category', 'object_id' => $category->id]);?>" class="nowrap"><i class="material-icons left">history</i>History</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['content/', 'category' => $category->id]);?>" class="nowrap"><i class="material-icons left">list</i>Content List</a>
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
