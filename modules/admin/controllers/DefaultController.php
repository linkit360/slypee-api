<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;

use app\models\Content;
use app\models\Category;
use yii\imagine\Image;

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
//
//
//        $rule = $auth->createPermission('createSlider');
//        $rule->description = 'Create a slider item';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('updateSlider');
//        $rule->description = 'Update slider item';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('viewSlider');
//        $rule->description = 'View list of slider items';
//        $auth->add($rule);
//
//        $rule = $auth->createPermission('viewSliderLog');
//        $rule->description = 'View slider log';
//        $auth->add($rule);

//        $adminRole = $auth->getRole('admin');
//
//        $rule = $auth->getPermission("viewCategoryLog");
//
//        $auth->addChild($adminRole, $rule);
//        $categories = Category::find()->all();
//        foreach($categories as $c) {
//            $count = Content::find()->andWhere(["category_id" => $c->id, "active" => 1])->count();
//            $c->content = $count;
//            $c->save(false);
//
//            print($c->id." | ".$c->content."<br/>");
//        }

        //die();

//        $content = Content::find()->all();
//        foreach ($content as $c) {
//            if(!file_exists($c->uploadPath . $c->thumbnail)) {
//                Image::thumbnail($c->uploadPath . $c->logo, 250, 250)->save($c->uploadPath . $c->thumbnail, ['jpeg_quality' => 95]);
//            }
//        }
//
//        die();

        return $this->render('index', [
            'user' => Yii::$app->user
        ]);
    }
}
