<?php
$baseUrl = "{$_SERVER["REQUEST_SCHEME"]}://{$_SERVER["HTTP_HOST"]}/api/";
?>
<div class="api-customer-status">
    <?=Yii::$app->customer->identity ? Yii::$app->customer->identity->username:"You are not logged in"?>
</div>
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
                List of content by category<br/>
                Headers:<br/>
                <strong>slypee-content-type</strong> - valid values are - <strong>free, subscription, single</strong><br/>
                <strong>slypee-content-ordering</strong> - valid values are - <strong>rating, -rating, top, -top, date</strong><br/>
                <strong>slypee-content-paging-start</strong> - Start position for content items<br/>
                <strong>slypee-content-paging-limit</strong> - Count of content items
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/search"?>"><?=$baseUrl."content/search"?></a>
            <p>
                Search:<br/>
                <strong>slypee-content-query</strong><br/>
                + pagination headers
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/top"?>"><?=$baseUrl."content/top"?></a>
            <p>
                Top charts<br/>
                Headers:<br/>
                <strong>slypee-content-category</strong> - category_id</strong><br/>
                + pagination headers
            </p>
        </li>
        <li class="api-list__item">
            Top charts by Category
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/ID"?>"><?=$baseUrl."content/ID"?></a>
            <p>
                Content item
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/customer"?>"><?=$baseUrl."content/customer"?></a>
            <p>
                List of customer content<br/>
                Headers:<br/>
                <strong>slypee-content-type</strong> - valid values are - <strong>subscription, single</strong><br/>
                <strong>slypee-content-ordering</strong> - valid values are - <strong>date, -date, name, -name</strong><br/>
                <strong>slypee-content-paging-start</strong> - Start position for content items<br/>
                <strong>slypee-content-paging-limit</strong> - Count of content items
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/subscribe/ID"?>"><?=$baseUrl."content/subscribe/ID"?></a>
            <p>Subscribe to content item <strong>(POST?)</strong></p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."content/unsubscribe/ID"?>"><?=$baseUrl."content/unsubscribe/ID"?></a>
            <p>Unsubscribe to content item <strong>(POST?)</strong></p>
        </li>
        <li class="api-list__header">Customers</li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."customer/signin"?>"><?=$baseUrl."customer/signin"?></a>
            <p>
                Sign in<br/>
                Method: <strong>POST</strong><br/>
                Data: <strong>email, password</strong>
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."customer/logout"?>"><?=$baseUrl."customer/logout"?></a>
            <p>
                Logout<br/>
                Method: <strong>GET</strong>
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."customer"?>"><?=$baseUrl."customer"?></a>
            <p>
                Sign up<br/>
                Method: <strong>POST</strong><br/>
                Data: <strong>username, email, password, password_confirm</strong>
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."customer"?>"><?=$baseUrl."customer"?></a>
            <p>
                Update profile<br/>
                Method: <strong>PUT</strong><br/>
                Data: <strong>username, email, old_password, password, password_confirm, avatar</strong>
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."customer"?>"><?=$baseUrl."customer"?></a>
            <p>
                Customer info<br/>
                Method: <strong>GET</strong><br/>
                Header: <strong>x-slypee-auth-token</strong>
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."customer/recovery"?>"><?=$baseUrl."customer/recovery"?></a>
            <p>
                Password recovery<br/>
                Method: <strong>POST</strong><br/>
                Data: <strong>email</strong>
            </p>
        </li>
        <li class="api-list__item">
            <a href="<?=$baseUrl."customer/recovery-confirm"?>"><?=$baseUrl."customer/recovery-confirm"?></a>
            <p>
                Password recovery confirm<br/>
                Method: <strong>POST</strong><br/>
                Data: <strong>token, password, password_confirm</strong>
            </p>
        </li>
    </ul>
</div>
