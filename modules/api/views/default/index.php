<?php
$baseUrl = "{$_SERVER["REQUEST_SCHEME"]}://{$_SERVER["HTTP_HOST"]}/api/";
?>
<div class="api-default-index">
    <h2>List of api methods:</h2>
    <ul class="api-list">
        <li class="api-list__header">Main page</li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."slider"?>"><?=$baseUrl."slider/"?></a>
            <p>Slider</p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."main"?>"><?=$baseUrl."main/"?></a>
            <p>Page content (main categories with content)</p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."category/menu"?>"><?=$baseUrl."category/menu"?></a>
            <p>List of categories for main menu</p>
        </li>
        <li class="api-list__header">Content</li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."category"?>"><?=$baseUrl."category"?></a>
            <p>List of categories</p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."category/SLUG"?>"><?=$baseUrl."category/SLUG"?></a>
            <p>Category info</p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/category/ID"?>"><?=$baseUrl."content/category/ID"?></a>
            <p>
                Content for category<br/>
                Headers for order and pagination .... coming soon
            </p>
        </li>
        <li class="api-list__item">Search</li>
        <li class="api-list__item">Top charts</li>
        <li class="api-list__item">Top charts by Category</li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/ID"?>"><?=$baseUrl."content/ID"?></a>
            <p>
                Content item
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."category/subscribe/ID"?>"><?=$baseUrl."category/subscribe/ID"?></a>
            <p>Subscribe to content item <strong>(POST?)</strong></p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."category/unsubscribe/ID"?>"><?=$baseUrl."category/unsubscribe/ID"?></a>
            <p>Unsubscribe to content item <strong>(POST?)</strong></p>
        </li>
        <li class="api-list__header">Customers</li>
        <li class="api-list__item"><p>Sign up</p></li>
        <li class="api-list__item"><p>Sign in</p></li>
        <li class="api-list__item">Edit</li>
        <li class="api-list__item"><p>Password recovery</p></li>
        <li class="api-list__item"><p>Customer Content</p></li>
        <li class="api-list__item"><p>Logout</p></li>
        <li class="api-list__item"><p><strong>Confirm links (Recovery, Sign up?)</strong></p></li>
    </ul>
</div>
