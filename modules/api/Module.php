<?php

namespace app\modules\api;

use Yii;
/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function beforeAction($action) {

        //$customer = Yii::$app->customer;

        //$is_guest = $customer->getIsGuest();

        return parent::beforeAction($action);
    }
}
