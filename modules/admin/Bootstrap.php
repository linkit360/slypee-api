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
                'admin/<controller:category|users|roles>' => 'admin/<controller>/index',
                'admin/<controller:category|users|roles>/add' => 'admin/<controller>/create',
                'admin/<controller:category|users>/view/<id:\d+>' => 'admin/<controller>/view',
                'admin/<controller:category|users>/activate/<id:\d+>' => 'admin/<controller>/activate',
                'admin/<controller:category|users>/update/<id:\d+>' => 'admin/<controller>/update',

                'admin/<controller:category>/content/<category_id:\d+>' => 'admin/<controller>/content',
                'admin/category-log/<category_id:\d+>' => 'admin/category-log/index',

                'admin/pagination/' => 'admin/per-page-settings/index',
                'admin/pagination/update/<id:\d+>' => 'admin/per-page-settings/update',

                'admin/roles/update/<name:\w+>' => 'admin/roles/update',
            ]
        );
    }
}