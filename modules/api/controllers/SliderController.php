<?php

namespace app\modules\api\controllers;

use yii\rest\ActiveController;


class SliderController extends ActiveController
{
    public $modelClass = 'app\models\Slider';

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
