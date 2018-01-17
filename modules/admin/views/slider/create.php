<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Slider', 'url' => ['slider/'], 'class' => 'breadcrumb'];
$this->params['breadcrumbs'][] = $this->title;

/* <?= $form->field($model, 'image')->fileInput() ?> */
?>
<h1><?= $this->title ?></h1>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'options' => [
            'id' => 'edit-form',
            'data-pjax' => true,
            'enctype' => 'multipart/form-data'
    ],
    'fieldConfig' => [
        'template' => "<div class=\"row\"><div class=\"input-field indigo-field\">{input}{label}{error}</div></div>",
        'labelOptions' => ['class' => '', 'data-error' => 'wrong'],
        'inputOptions' => ['class' => 'validate']
    ],
    'errorCssClass' => 'error'
]); ?>


<?= $form->field($model, 'title')->input('text', [
    'style' => 'width: 30%'
]) ?>

<?= $form->field($model, 'subtitle')->input('text', [
    'style' => 'width: 30%'
]) ?>

<?= $form->field($model, 'description')->textarea([
    "rows" => 5,
    "cols" => 10,
    "class" => "materialize-textarea"
]) ?>

<?= $form->field($model, 'link')->input('text', []) ?>

<?= $form->field($model, 'image')->fileInput() ?>

<div class="form-group flex flex_end">
    <a class="btn orange waves-effect waves-light" href="javascript:history.back()" style="margin-right: 20px">Cancel</a>
    <?= Html::submitButton($btn, ['class' => 'btn btn-primary green waves-effect waves-light']) ?>
</div>

<div class="form-summary"></div>

<div class="preloader-wrapper small" id="edit-loader">
    <div class="spinner-layer spinner-green-only">
        <div class="circle-clipper left">
            <div class="circle"></div>
        </div><div class="gap-patch">
            <div class="circle"></div>
        </div><div class="circle-clipper right">
            <div class="circle"></div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
