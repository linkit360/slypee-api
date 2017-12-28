<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Sign in';
?>
<div class="container">
    <div class="login-wrap grey lighten-4 z-depth-1 row">

        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "<div class=\"row\"><div class=\"input-field indigo-field\">{input}{label}{error}</div></div>",
                'labelOptions' => ['class' => '', 'data-error' => 'wrong'],
                'inputOptions' => ['class' => 'validate']
            ],
            'errorCssClass' => 'error'
        ]); ?>

        <div class="row">
            <?= $form->field($model, 'username')->textInput()?>
        </div>

        <div class="row">
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"row remember-me\">{input} {label}</div>",
            'class' => "filled-in indigo-field"
        ]) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Login', ['style' => 'width: 100%', 'class' => 's12 btn btn-large waves-effect indigo', 'name' => 'login-button']) ?>
            </div>

            <div class="form-summary"></div>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="progress-wrap" id="login-progress">
            <div class="progress indigo">
                <div class="indeterminate indigo lighten-3"></div>
            </div>
        </div>

    </div>
</div>
