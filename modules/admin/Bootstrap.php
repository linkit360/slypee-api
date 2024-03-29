<?php
namespace app\modules\admin;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules(
            [
                // объявление правил здесь
                // '' => 'site/default/index',
                // '<_a:(about|contacts)>' => 'site/default/<_a>'
                'admin/<controller:category|content|customers|users|roles|slider>' => 'admin/<controller>/index',
                'admin/<controller:category|content|customers|users|roles|slider>/add' => 'admin/<controller>/create',
                'admin/<controller:category|content|customers|users|slider>/view/<id:\d+>' => 'admin/<controller>/view',
                'admin/<controller:category|content|customers|users|slider>/reactivate/<id:\d+>' => 'admin/<controller>/reactivate',
                'admin/<controller:category|content|customers|users|slider>/update/<id:\d+>' => 'admin/<controller>/update',

                'admin/customers/content/<id:\d+>' => 'admin/customers/content',

                'admin/<controller:category>/content/<category_id:\d+>' => 'admin/<controller>/content',

                'admin/pagination/' => 'admin/per-page-settings/index',
                'admin/pagination/update/<id:\d+>' => 'admin/per-page-settings/update',

                'admin/roles/update/<name:\w+>' => 'admin/roles/update',

                // - to _
                'admin/currency_types' => 'admin/currency-types/index',
                'admin/currency_types/add' => 'admin/currency-types/create',
                'admin/currency_types/update/<id:\d+>' => 'admin/currency-types/update',
                'admin/content_types' => 'admin/content-types/index',
                'admin/content_types/add' => 'admin/content-types/create',
                'admin/content_types/update/<id:\d+>' => 'admin/content-types/update',

                // log
                'admin/log/<type:\w+>' => 'admin/log/index',
            ]
        );
    }
}