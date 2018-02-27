<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use yii\widgets\LinkPager;
use app\modules\admin\widgets\ActionCheckbox;
use app\modules\admin\widgets\ActionInput;
use app\modules\admin\widgets\ActionPanel;
use app\modules\admin\widgets\AddNewItem;


$this->title = 'Customers';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    if (Yii::$app->user->can('createCustomer')) {
        ?>
        <?= AddNewItem::widget(["link" => Url::to(['customers/add'])]) ?>
        <?php
    }
    ?>
</div>

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method' => 'GET',
        'action' => Url::to(['customers/']),
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
                <div data-type="email" style="display: none">
                    <?= $form->field($search, 'email')->input('text', [
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
        <?= Html::resetButton('Reset filter', ['class' => 'btn orange', 'style' => 'margin-right: 20px', 'onclick'=>"window.location = '".Url::to(['customers/'])."'"]) ?>
        <?= Html::submitButton("Apply filter", ['class' => 'btn btn-primary green waves-effect waves-light']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= ActionPanel::widget(["activate" => "/admin/customers/activate", "deactivate" => "/admin/customers/deactivate", "edit" => "/admin/customers/ajax-update"]) ?>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 3%">
                <input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" id="itemall" />
                <label for="itemall"></label>
            </th>
            <th><?= $sort->link('id', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('username', ["class" => "sort-link"]) ?>
            <th><?= $sort->link('email', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('active', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('created_at', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('updated_at', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('content', ["class" => "sort-link"]) ?></th>
            <th style="width: 10%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr <?= $user->active ? "class=\"active\"":"" ?> data-content="0" data-id="<?= $user->id?>">
                <td>
                    <input type="checkbox" class="filled-in action-checkbox indigo-field item-checkbox" value="<?=$user->id?>" id="item<?= $user->id?>"/>
                    <label for="item<?= $user->id?>"></label>
                </td>
                <td><?= $user->id ?></td>
                <td><?= ActionInput::widget(["item" => $user, "property" => "username"]) ?></td>
                <td><?= ActionInput::widget(["item" => $user, "property" => "email"]) ?></td>
                <td><?= ActionCheckbox::widget(["item" => $user, "property" => "active"]) ?></td>
                <td class="nowrap grey-text darken-1"><?= date("m-d-y", $user->created_at) ?></td>
                <td class="nowrap grey-text darken-1"><?= date("m-d-y", $user->updated_at) ?></td>
                <td><?= $user->content ?></td>
                <td>
                    <div class="dropdown-wrapper">
                        <a class="dropdown-button nowrap" href="#!" data-constrainwidth="false" data-activates="dropdown<?= $user->id ?>">Actions<i class="material-icons right">arrow_drop_down</i></a>
                        <ul class="dropdown-content" id="dropdown<?= $user->id ?>">
                            <li>
                                <a href="<?=Url::to(['customers/view', 'id' => $user->id]);?>" class="nowrap"><i class="material-icons left">remove_red_eye</i>View</a>
                            </li>
                            <?php
                            if (Yii::$app->user->can('updateCustomer')) {
                                ?>
                                <li>
                                    <a href="<?= Url::to(['customers/update', 'id' => $user->id]); ?>" class="nowrap"><i
                                                class="material-icons left">edit</i>Update</a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(['customers/reactivate', 'id' => $user->id]); ?>"
                                       class="nowrap activate">
                                        <i class="material-icons left">check</i>
                                        <span data-active="Deactivate"
                                              data-nonactive="Activate"><?= $user->active ? "Deactivate" : "Activate" ?></span>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                            if (Yii::$app->user->can('viewCustomerLog')) {
                                ?>
                                <li>
                                    <a href="<?= Url::to(['log/customer', 'object_id' => $user->id]); ?>" class="nowrap"><i class="material-icons left">history</i>History</a>
                                </li>
                                <?php
                            }
                            ?>
                            <li>
                                <a href="<?=Url::to(['customers/content', 'id' => $user->id]);?>" class="nowrap"><i class="material-icons left">shopping_cart</i>Content list</a>
                            </li>
                        </ul>
                    </div>
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