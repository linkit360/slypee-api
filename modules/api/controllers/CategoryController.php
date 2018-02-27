<?php

namespace app\modules\api\controllers;

use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class CategoryController extends ActiveController
{
    public $modelClass = 'app\models\Category';

    public function actions()
    {
        $actions = parent::actions();

        $actions["index"]["prepareDataProvider"] = function() {
            return new ActiveDataProvider([
                'query' => Category::find()->andWhere(["active" => 1])->andWhere([">", "content", 0]),
                'sort' => [
                    'defaultOrder' => [
                        'priority' => SORT_ASC
                    ]
                ]
            ]);
        };

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

    public function actionMenu() {
        return Category::find()->select(["id", "name", "slug"])->andWhere([">", "content", 0])->andWhere(["=", "main_menu", 1])->andWhere(["=", "active", 1])->orderBy("priority ASC")->all();
    }

    public function actionInfo($slug)
    {
        $category = Category::find()->where(["active" => 1, "slug" => $slug])->one();
        if(!$category) {
            throw new NotFoundHttpException('Category not found', 404);
        }

        return $category;
    }
}
