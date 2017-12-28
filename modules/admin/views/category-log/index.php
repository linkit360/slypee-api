<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;

$this->title = $category->name.' category log';
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['category/index'], 'class' => 'breadcrumb'];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>
</div>

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method' => 'GET',
        'action' => Url::to(['category-log/index', 'category_id' => $category->id]),
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

        </div>

        <div class="row flex flex_end">
            <?= Html::resetButton('Reset filter', ['class' => 'btn orange', 'style' => 'margin-right: 20px', 'onclick'=>"window.location = '".Url::to(['category-log/index', 'category_id' => $category->id])."'"]) ?>
            <?= Html::submitButton("Apply filter", ['class' => 'btn btn-primary green waves-effect waves-light']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 15%">Datetime</th>
            <th style="width: 25%">User</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td class="nowrap grey-text darken-1"><?= date("m-d-y H:i:s", $log->datetime) ?></td>
                <td><?= $log->user->username ?></td>
                <td><?= $log->category->name ?></td>
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