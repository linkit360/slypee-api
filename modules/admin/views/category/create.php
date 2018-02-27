<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

use dosamigos\tinymce\TinyMce;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['category/'], 'class' => 'breadcrumb'];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= $this->title ?></h1>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'options' => [
            'id' => 'edit-form',
            'data-pjax' => true,
    ],
    'fieldConfig' => [
        'template' => "<div class=\"row\"><div class=\"input-field indigo-field\">{input}{label}{error}</div></div>",
        'labelOptions' => ['class' => '', 'data-error' => 'wrong'],
        'inputOptions' => ['class' => 'validate']
    ],
    'errorCssClass' => 'error'
]); ?>

<?= $form->field($model, 'active')->checkbox([
    'template' => "<div class=\"row\">{input} {label}</div>",
    'class' => "filled-in indigo-field",
    'uncheck' => 0,
    'check' => 1
]) ?>

<?= $form->field($model, 'name')->input('text', [
    'style' => 'width: 30%'
]) ?>

<?= $form->field($model, 'description')->widget(TinyMce::className(), [
    'options' => ['rows' => 16],
    'clientOptions' => [
        'plugins' => [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    ]
]);?>

<?= $form->field($model, 'main_page')->checkbox([
    'template' => "<div class=\"row\">{input} {label}</div>",
    'class' => "filled-in indigo-field",
    'uncheck' => 0,
    'check' => 1
]) ?>

<?= $form->field($model, 'main_menu')->checkbox([
    'template' => "<div class=\"row\">{input} {label}</div>",
    'class' => "filled-in indigo-field",
    'uncheck' => 0,
    'check' => 1
]) ?>


<div class="form-group flex flex_end">
    <a class="btn orange waves-effect waves-light" href="javascript:history.back()" style="margin-right: 20px">Cancel</a>
    <?= Html::submitButton($btn, ['class' => 'btn btn-primary green waves-effect waves-light']) ?>
</div>

<div class="form-summary">
    <?= $form->errorSummary($model, ["header" => "<span class=\"general-error\">Please fix all errors</span>"]); ?>
</div>

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
