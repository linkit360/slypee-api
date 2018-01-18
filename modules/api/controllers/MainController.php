<?php

namespace app\modules\api\controllers;

use yii\rest\ActiveController;

use app\models\Category;
use app\models\Content;


class MainController extends ActiveController
{
    public $modelClass = false;

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionIndex() {
        $data = [];

        $categories = Category::find()->select(["id", "name"])->andWhere(["=", "main_page", 1])->andWhere(["=", "active", 1])->orderBy("priority")->all();

        foreach ($categories as $category) {
            $items = Content::find()->andWhere(['category_id' => $category->id])->andWhere(['active' => 1])->orderBy("rating DESC")->offset(0)->limit(20)->all();

            $data[] = [
                "category" => $category,
                "items" => $items,
            ];
        }

        return $data;
    }
}
