<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;

$this->title = $object ? "{$object->name} $type log":"$type log";
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1 class="first-letter"><?= Html::encode($this->title) ?></h1>
</div>

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method' => 'GET',
        'action' => Url::to(['category-log/']),
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
                <?= $form->field($search, 'date_begin')->input('text', [
                    'class' => 'date_picker'
                ]) ?>
            </div>

            <div class="cell">
                <?= $form->field($search, 'date_end')->input('text', [
                    'class' => 'date_picker'
                ]) ?>
            </div>

            <div class="cell">
                <?= $form->field($search, 'user_id')->dropDownList($users, [
                    'class' => 'materialize-select',
                    'prompt'=>'---'
                ]) ?>
            </div>

        </div>

        <div class="row flex flex_end">
            <?= Html::resetButton('Reset filter', ['class' => 'btn orange', 'style' => 'margin-right: 20px', 'onclick'=>"window.location = '".Url::to(['category-log/'])."'"]) ?>
            <?= Html::submitButton("Apply filter", ['class' => 'btn btn-primary green waves-effect waves-light']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 15%"><?= $sort->link('datetime', ["class" => "sort-link"]) ?></th>
            <th style="width: 25%"><?= $sort->link('user.username', ["class" => "sort-link", "label" => "User"]) ?></th>
            <th><?= $sort->link($object_field, ["class" => "sort-link", "label" => "Category"]) ?></th>
            <th><?= $sort->link('crud_types.name', ["class" => "sort-link", "label" => "Type"]) ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td class="nowrap grey-text darken-1"><?= date("m-d-y H:i:s", $log->datetime) ?></td>
                <td><?= $log->user->username ?></td>
                <td><?= $log->$type->$field ?></td>
                <td class="upper"><?= $log->crudType->name ?></td>
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