<?php

namespace app\modules\api\controllers;

use app\models\Slider;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;


class SliderController extends ActiveController
{
    public $modelClass = 'app\models\Slider';

    public function actions()
    {
        $actions = parent::actions();

        $actions["index"]["prepareDataProvider"] = function() {
            return new ActiveDataProvider([
                'query' => Slider::find()->andWhere(["active" => 1]),
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
}
