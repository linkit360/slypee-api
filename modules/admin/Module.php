<?php

namespace app\modules\admin;

use Yii;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        Yii::$app->user->loginUrl = '/admin/login/';
        Yii::$app->homeUrl = '/admin/';

        $this->layout = 'admin';
    }

    public function beforeAction($action) {

        $login_required = false;
        $user = Yii::$app->user;

        if(Yii::$app->controller->id != "login" && $user->getIsGuest())
        {
            $user->loginRequired();
            $login_required = true;

        }

        if(Yii::$app->controller->id != "login" && !$login_required) {

            if(!$user->identity->active) {
                $user->logout();
                $user->loginRequired();
            }

        }

        if(!$user->getIsGuest()) {
            $user_id = $user->identity->id;
            $roles = Yii::$app->authManager->getRolesByUser($user_id);
            foreach ($roles as $r) {
                if ($r->name == "Admin") {
                    Yii::$app->user->identity->is_admin = true;
                }
            }
        }

        return parent::beforeAction($action);
    }
}
