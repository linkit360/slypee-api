<?php
use yii\helpers\Html;
?>
<h1>Admin Categories</h1>
<ul>
    <?php foreach ($categories as $category): ?>
        <li>
            <?= Html::encode("{$category->name}") ?>:
        </li>
    <?php endforeach; ?>
</ul>
