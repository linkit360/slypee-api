<?php

use yii\helpers\Html;

?>
<div class="table-actions">
    <div class="table-actions__wrapper">
        <div class="table-actions__label">
            Selected items: <strong id="selected_count" class="selected_count">0</strong>
        </div>

        <div class="table-actions__buttons table-actions__buttons_active">
            <?= Html::Button("Activate selected", [
                'class' => 'btn btn-primary green waves-effect waves-light table-actions_button',
                'id' => 'activate-action',
                'data-href' => $activate
            ])
            ?>
            <?= Html::Button("Deactivate selected", [
                'class' => 'btn btn-primary orange waves-effect waves-light table-actions_button',
                'id' => 'deactivate-action',
                'data-href' => $deactivate,
                'data-note' => 'Are you sure you want to deactivate category(s) with Content. This Content will be not available for customers.'
            ])
            ?>
            <?= Html::Button("Edit selected", ['class' => 'btn btn-primary green waves-effect waves-light table-actions_button', 'id' => 'edit-action']) ?>
        </div>

        <div class="table-actions__buttons">
            <?= Html::Button("Apply changes", [
                'class' => 'btn btn-primary green waves-effect waves-light table-actions_button',
                'id' => 'apply-edit-action',
                'data-href' => $edit
            ])
            ?>
            <?= Html::Button("Cancel", ['class' => 'btn btn-primary orange waves-effect waves-light table-actions_button', 'id' => 'cancel-edit-action']) ?>
        </div>
    </div>

    <div class="table-actions__errors red darken-4">
    </div>
</div>