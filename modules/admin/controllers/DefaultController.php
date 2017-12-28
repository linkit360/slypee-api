<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;

        // добавляем разрешение "createPost"
//        $rule = $auth->createPermission('createUser');
//        $rule->description = 'Create a user';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('updateUser');
//        $rule->description = 'Update user';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('viewUser');
//        $rule->description = 'View list of users';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('viewUserLog');
//        $rule->description = 'View users log';
//        $auth->add($rule);

//        $adminRole = $auth->getRole('admin');
//
//        $rule = $auth->getPermission("viewCategoryLog");
//
//        $auth->addChild($adminRole, $rule);

        return $this->render('index', [
            'user' => Yii::$app->user
        ]);
    }
}
