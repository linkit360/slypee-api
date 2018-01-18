<?php

namespace app\modules\api\controllers;

use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;


class CategoryController extends ActiveController
{
    public $modelClass = 'app\models\Category';

    public function actions()
    {
        $actions = parent::actions();

        $actions["index"]["prepareDataProvider"] = function() {
            return new ActiveDataProvider([
                'query' => Category::find()->andWhere(["active" => 1]),
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
        return Category::find()->select(["id", "name"])->andWhere(["=", "main_menu", 1])->andWhere(["=", "active", 1])->orderBy("priority")->all();
    }
}
