<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

use dosamigos\tinymce\TinyMce;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Content', 'url' => ['content/'], 'class' => 'breadcrumb'];
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


<?= $form->field($model, 'name')->input('text', [
    'style' => 'width: 30%'
]) ?>

<?= $form->field($model, 'category_id')->dropDownList($categories, [
    'class' => 'materialize-select'
]) ?>

<?= $form->field($model, 'currency_type_id')->dropDownList($currency_types, [
    'class' => 'materialize-select'
]) ?>

<?= $form->field($model, 'content_type_id')->dropDownList($content_types, [
    'class' => 'materialize-select'
]) ?>

<?= $form->field($model, 'producer')->input('text', []) ?>

<?= $form->field($model, 'price')->input('text', [
]) ?>

<?= $form->field($model, 'rating')->input('text', [
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

<?= $form->field($model, 'video')->input('text', [
    'style' => 'width: 30%'
]) ?>

<?php
if($model->logo) {
    ?>
    <img src="/<?= $model->uploadPath.$model->logo ?>" alt="" style="max-width: 100%" />
    <?php
}
?>

<div class="field-logo">
    <div class="file-field input-field">
        <div class="btn">
            <span>Logo</span>
            <input type="file" name="logo" />
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text">
        </div>
    </div>
</div>

<?= $form->field($model, 'active')->checkbox([
    'template' => "<div class=\"row\">{input} {label}</div>",
    'class' => "filled-in indigo-field",
    'uncheck' => 0,
    'check' => 1
]) ?>

<?php
    if($model->contentPhotos) {
        ?>
        <div class="table-content">
            <table class="striped responsive-table">
                <?php
                    foreach ($model->contentPhotos as $photo) {
                        ?>
                        <tr>
                            <td class="ordering" style="width: 35px"></td>
                            <td>
                                <img src="/<?=$photo->photo->thumbnail?>" height="60" />
                            </td>
                            <td style="width: 5%">
                                <span class="material-icons remove-photo hand" data-content="<?=$model->id?>" data-id="<?=$photo->id?>">delete</span>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>
        <?php
    }
?>

<div class="dropzone-slypee"  id="slypee-dropzone">
    Add screenshots of content
</div>

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