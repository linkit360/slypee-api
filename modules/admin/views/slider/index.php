<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use app\modules\admin\widgets\AddNewItem;
use app\modules\admin\widgets\ActionInput;
use app\modules\admin\widgets\ActionPanel;
use app\modules\admin\widgets\ActionCheckbox;

$this->title = 'Slider';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    if (Yii::$app->user->can('createSlider')) {
        ?>
        <?= AddNewItem::widget(["link" => Url::to(['slider/add'])]) ?>
        <?php
    }
    ?>
</div>

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method' => 'GET',
        'action' => Url::to(['slider/']),
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
                    "email" => "Email",
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

    <div class="row flex flex_end">
        <?= Html::resetButton('Reset filter', ['class' => 'btn orange', 'style' => 'margin-right: 20px', 'onclick'=>"window.location = '".Url::to(['slider/'])."'"]) ?>
        <?= Html::submitButton("Apply filter", ['class' => 'btn btn-primary green waves-effect waves-light']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ActionPanel::widget(["activate" => "/admin/slider/activate", "deactivate" => "/admin/slider/deactivate", "edit" => "/admin/slider/ajax-update"]) ?>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 3%">
                <input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" id="itemall" />
                <label for="itemall"></label>
            </th>
            <th style="width: 35px">&nbsp;</th>
            <th style="width: 60%">Name</th>
            <th>Active</th>
            <th>Order</th>
            <th style="width: 20%">&nbsp;</th>
        </tr>
        </thead>
        <tbody class="sortable" data-href="<?=Url::to(['slider/top']);?>">
        <?php foreach ($slider as $item): ?>
            <tr <?= $item->active ? "class=\"active\"":"" ?> data-content="0" data-id="<?= $item->id?>">
                <td>
                    <input type="checkbox" class="filled-in action-checkbox indigo-field item-checkbox" value="<?=$item->id?>" id="item<?= $item->id?>"/>
                    <label for="item<?= $item->id?>"></label>
                </td>
                <td class="ordering"></td>
                <td><?= ActionInput::widget(["item" => $item, "property" => "title"]) ?></td>
                <td><?= ActionCheckbox::widget(["item" => $item, "property" => "active"]) ?></td>
                <td class="priority"><?= $item->priority ?></td>
                <td style="text-align: right;">
                    <a class="dropdown-button nowrap" href="#!" data-constrainwidth="false" data-activates="dropdown<?= $item->id ?>">Actions<i class="material-icons right">arrow_drop_down</i></a>
                    <ul class="dropdown-content" id="dropdown<?= $item->id ?>">
                        <li>
                            <a href="<?=Url::to(['slider/view', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">remove_red_eye</i>View</a>
                        </li>
                        <?php
                        if (Yii::$app->user->can('updateSlider')) {
                            ?>
                            <li>
                                <a href="<?= Url::to(['slider/update', 'id' => $item->id]); ?>" class="nowrap"><i
                                            class="material-icons left">edit</i>Update</a>
                            </li>
                            <li>
                                <a href="<?= Url::to(['slider/reactivate', 'id' => $item->id]); ?>" class="nowrap activate"><i class="material-icons left">check</i><span data-active="Deactivate" data-nonactive="Activate"><?= $item->active ? "Deactivate" : "Activate" ?></span>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                        <?php
                        if (Yii::$app->user->can('viewSliderLog')) {
                            ?>
                            <li>
                                <a href="<?= Url::to(['log/slider', 'object_id' => $item->id]); ?>" class="nowrap"><i class="material-icons left">history</i>History</a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
