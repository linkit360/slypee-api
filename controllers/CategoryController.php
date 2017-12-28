<?php

namespace app\controllers;
use app\models\Category;

use yii\rest\ActiveController;

class CategoryController extends ActiveController
{
    public $modelClass = 'app\models\Category';

    public function behaviors()
    {
        return [
            [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index', 'view'],
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }
}
