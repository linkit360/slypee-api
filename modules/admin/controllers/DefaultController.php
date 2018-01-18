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
//        $rule = $auth->createPermission('createSlider');
//        $rule->description = 'Create a content';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('updateSlider');
//        $rule->description = 'Update content';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('viewSlider');
//        $rule->description = 'View list of content';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('viewSliderLog');
//        $rule->description = 'View content log';
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
