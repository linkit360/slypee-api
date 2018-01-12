<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use app\modules\admin\widgets\ActionCheckbox;
use app\modules\admin\widgets\AddNewItem;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= AddNewItem::widget(["link" => Url::to(['users/add'])]) ?>
</div>


<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 3%"><!-- --><input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" />
                <label for="filled-in-box"><!-- --></label><!-- --></th>
            <th><?= $sort->link('id', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('name', ["class" => "sort-link"]) ?>
            <th><?= $sort->link('email', ["class" => "sort-link"]) ?></th>
            <th>Role</th>
            <th><?= $sort->link('active', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('created_at', ["class" => "sort-link"]) ?></th>
            <th><?= $sort->link('updated_at', ["class" => "sort-link"]) ?></th>
            <th style="width: 10%">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" /><label for="filled-in-box"><!-- --></label></td>
                <td><?= $user->id ?></td>
                <td><?= $user->username ?></td>
                <td><?= $user->email ?></td>
                <td class="upper"><?= $user->roleName ?></td>
                <td><?= $user->active ? "Yes":"No"?></td>
                <td class="nowrap grey-text darken-1"><?= date("m-d-y", $user->created_at) ?></td>
                <td class="nowrap grey-text darken-1"><?= date("m-d-y", $user->updated_at) ?></td>
                <td>
                    <div class="dropdown-wrapper">
                        <a class="dropdown-button nowrap" href="#!" data-constrainwidth="false" data-activates="dropdown<?= $user->id ?>">Actions<i class="material-icons right">arrow_drop_down</i></a>
                        <ul class="dropdown-content" id="dropdown<?= $user->id ?>">
                            <li>
                                <a href="<?=Url::to(['users/view', 'id' => $user->id]);?>" class="nowrap"><i class="material-icons left">remove_red_eye</i>View</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['users/update', 'id' => $user->id]);?>" class="nowrap"><i class="material-icons left">edit</i>Update</a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['users/activate', 'id' => $user->id]);?>" class="nowrap"><i class="material-icons left">check</i><?= $user->active ? "Deactivate" : "Activate"?></a>
                            </li>
                            <li>
                                <a href="<?=Url::to(['users-log/', 'category_id' => $user->id]);?>" class="nowrap"><i class="material-icons left">history</i>History</a>
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