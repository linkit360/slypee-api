<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\widgets\AddNewItem;
use app\modules\admin\widgets\ActionInput;
use app\modules\admin\widgets\ActionPanel;
use app\modules\admin\widgets\ActionCheckbox;

$this->title = 'Slider';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="flex flex_centered flex_between">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= AddNewItem::widget(["link" => Url::to(['slider/add'])]) ?>
</div>

<?= ActionPanel::widget(["activate" => "/admin/slider/activate", "deactivate" => "/admin/slider/deactivate", "edit" => "/admin/slider/ajax-update"]) ?>

<div class="table-content">
    <table class="striped responsive-table">
        <thead>
        <tr>
            <th style="width: 3%">
                <input type="checkbox" class="filled-in action-checkbox indigo-field" value="1" id="itemall" />
                <label for="itemall"></label>
            </th>
            <th style="width: 35px">&nbsp;</th>
            <th style="width: 60%">Name</th>
            <th>Active</th>
            <th>Order</th>
            <th style="width: 20%">&nbsp;</th>
        </tr>
        </thead>
        <tbody class="sortable" data-href="<?=Url::to(['slider/top']);?>">
        <?php foreach ($slider as $item): ?>
            <tr <?= $item->active ? "class=\"active\"":"" ?> data-content="0" data-id="<?= $item->id?>">
                <td>
                    <input type="checkbox" class="filled-in action-checkbox indigo-field item-checkbox" value="<?=$item->id?>" id="item<?= $item->id?>"/>
                    <label for="item<?= $item->id?>"></label>
                </td>
                <td class="ordering"></td>
                <td><?= ActionInput::widget(["item" => $item, "property" => "title"]) ?></td>
                <td><?= ActionCheckbox::widget(["item" => $item, "property" => "active"]) ?></td>
                <td class="priority"><?= $item->priority ?></td>
                <td style="text-align: right;">
                    <a class="dropdown-button nowrap" href="#!" data-constrainwidth="false" data-activates="dropdown<?= $item->id ?>">Actions<i class="material-icons right">arrow_drop_down</i></a>
                    <ul class="dropdown-content" id="dropdown<?= $item->id ?>">
                        <li>
                            <a href="<?=Url::to(['slider/view', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">remove_red_eye</i>View</a>
                        </li>
                        <li>
                            <a href="<?=Url::to(['slider/update', 'id' => $item->id]);?>" class="nowrap"><i class="material-icons left">edit</i>Update</a>
                        </li>
                        <li>
                            <a href="<?=Url::to(['slider/reactivate', 'id' => $item->id]);?>" class="nowrap activate">
                                <i class="material-icons left">check</i>
                                <span data-active="Deactivate" data-nonactive="Activate"><?= $item->active ? "Deactivate" : "Activate"?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?=Url::to(['log/', 'type' => 'slider', 'object_id' => $item->id]);?>" class="nowrap"><i class="material-icons left">history</i>History</a>
                        </li>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
