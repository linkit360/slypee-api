<div class="action-input-el">
    <div class="action-input-active">
        <input type="hidden" value="<?= $item[$property] ?>" />
        <input type="text" name="<?= $property ?>" value="<?= $item[$property]?>" class="action-input" autocomplete="off" />
    </div>
    <div class="action-input-text">
        <?= $item[$property]?>
    </div>
</div>