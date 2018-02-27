<?php

use yii\helpers\Html;
use yii\helpers\Url;

use app\modules\admin\widgets\AddNewItem;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Content', 'url' => ['content/'], 'class' => 'breadcrumb'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flex flex_centered flex_between">
    <h1><?= $this->title ?> &mdash; <?= $model->name?></h1>
    <?php
    if (Yii::$app->user->can('createContent')) {
        ?>
        <?= AddNewItem::widget(["label" => "Add new content", "link" => Url::to(['content/add'])]) ?>
        <?php
    }
    ?>
</div>

<table class="striped responsive-table" style="margin: 25px 0 0">
    <tr>
        <td style="width: 15%">Name:</td>
        <td><?= $model->name?></td>
    </tr>
    <tr>
        <td style="width: 15%">Category:</td>
        <td><?= $model->category->name?></td>
    </tr>
    <tr>
        <td style="width: 15%">Currency type:</td>
        <td><?= $model->currencyType->name?></td>
    </tr>
    <tr>
        <td style="width: 15%">Content type:</td>
        <td><?= $model->contentType->name?></td>
    </tr>
    <tr>
        <td style="width: 15%">Price:</td>
        <td><?= $model->price?></td>
    </tr>
    <tr>
        <td style="width: 15%">Rating:</td>
        <td><?= $model->rating?></td>
    </tr>
    <tr>
        <td style="width: 15%">Producer:</td>
        <td><?= $model->producer?></td>
    </tr>
    <tr>
        <td>Description:</td>
        <td><?= $model->description ?></td>
    </tr>
    <tr>
        <td>Video:</td>
        <td><?= $model->video ?></td>
    </tr>
    <tr>
        <td>Logo:</td>
        <td><img src="/<?= $model->uploadPath . $model->logo ?>" style="height: 100px;" /></td>
    </tr>
    <tr>
        <td>
            Related content
        </td>
        <td>
            <table class="striped responsive-table">
                <tbody data-id="<?=$model->id?>">
                <?php
                if($model->related) {
                    foreach ($model->related as $r) {
                        ?>
                        <tr data-content="<?=$r["content"]?>">
                            <td style="width: 10%"><img src="<?=$r["logo"]?>" class="related-content-logo" /></td>
                            <td><?=$r["name"]?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            Screenshots
        </td>
        <td>
            <?php
            foreach ($model->contentPhotos as $photo) {
            ?>
                <img src="/<?=$photo->photo->thumbnail?>" style="height: 100px; margin-right: 10px;" />
            <?php
            }
            ?>
        </td>
    </tr>
</table>

<div class="flex flex_end" style="margin: 25px 0 0">
    <a class="btn orange waves-effect waves-light" href="<?=Url::to(['content/']);?>" style="margin-right: 20px">Back</a>
    <?php
    if (Yii::$app->user->can('updateContent')) {
        ?>
        <a class="btn green waves-effect waves-light" href="<?=Url::to(['content/update', 'id' => $model->id]);?>">Edit</a>
        <?php
    }
    ?>
</div>
