<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\modules\admin\assets\AdminAsset;

AdminAsset::register($this);

if(!Yii::$app->user->isGuest) {

    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    </head>
    <body>
    <?php $this->beginBody() ?>

    <div class="navbar-fixed">
        <nav class="indigo">
            <div class="nav-wrapper">
                <a href="/admin/" class="logo">Slypee CMS</a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li class="navbar-item-separator">
                        <i class="material-icons left">person</i> <?= Yii::$app->user->identity ? Yii::$app->user->identity->username : "" ?>
                    </li>
                    <li><a href="/admin/logout/">Logout</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="row">

        <div class="col s3 aside">
            <div class="collection  with-header">
                <?php
                if (Yii::$app->user->can('viewCategory')) {
                    ?>
                    <a href="<?= Url::to(['category/']); ?>" class="collection-item">Categories</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('updateCategory')) {
                    ?>
                    <a href="<?= Url::to(['category/top']); ?>" class="collection-item">Top categories</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewContent')) {
                    ?>
                    <a href="<?= Url::to(['content/']); ?>" class="collection-item">Content</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('updateContent')) {
                    ?>
                    <a href="<?= Url::to(['content/top']); ?>" class="collection-item">Top charts</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewUser')) {
                    ?>
                    <a href="<?= Url::to(['users/']); ?>" class="collection-item">Users</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewCustomer')) {
                    ?>
                    <a href="<?= Url::to(['customers/']); ?>" class="collection-item">Customers</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewSlider')) {
                    ?>
                    <a href="<?= Url::to(['slider/']); ?>" class="collection-item">Slider</a>
                    <?php
                }
                ?>

                <div class="collection-header"><h4>Settings</h4></div>

                <?php
                if (Yii::$app->user->identity->is_admin) {
                    ?>
                    <a href="<?= Url::to(['per-page-settings/']); ?>" class="collection-item">Pagination settings</a>
                    <a href="<?= Url::to(['roles/']); ?>" class="collection-item">Roles</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewCategoryLog')) {
                    ?>
                    <a href="<?= Url::to(['log/category']); ?>" class="collection-item">Category log</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewContentLog')) {
                    ?>
                    <a href="<?= Url::to(['log/content']); ?>" class="collection-item">Content log</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewUserLog')) {
                    ?>
                    <a href="<?= Url::to(['log/user']); ?>" class="collection-item">Users log</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewCustomerLog')) {
                    ?>
                    <a href="<?= Url::to(['log/customer']); ?>" class="collection-item">Customers log</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->can('viewSliderLog')) {
                    ?>
                    <a href="<?= Url::to(['log/slider']); ?>" class="collection-item">Slider log</a>
                    <?php
                }
                ?>

                <?php
                if (Yii::$app->user->identity->is_admin) {
                    ?>
                    <div class="collection-header"><h4>Service</h4></div>
                    <a href="<?= Url::to(['currency_types/']); ?>" class="collection-item">Currency types</a>
                    <?php
                }
                ?>

            </div>
        </div>

        <div class="col s9 main">
            <?= Breadcrumbs::widget([
                'homeLink' => [
                    'label' => 'Admin',
                    'class' => 'breadcrumb',
                    'url' => Yii::$app->homeUrl,
                ],
                'options' => [
                    'class' => 'breadcrumbs'
                ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'tag' => 'div',
                'itemTemplate' => '{link}',
                'activeItemTemplate' => '<span class="breadcrumb">{link}</span>'
            ]) ?>
            <?= $content ?>
        </div>

    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>

    <?php
}
?>