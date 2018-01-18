<?php

namespace app\modules\api\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

use app\models\Content;
use app\models\CustomersContent;

class ContentController extends ActiveController
{
    public $modelClass = 'app\models\Content';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['view']);

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

    public function actionIndex()
    {
        $preparedContent = [];
        $content = Content::find()->where(['active' => 1])->all();

        $url = Yii::$app->urlManager->createAbsoluteUrl(['/']);
        foreach($content as $item) {

            $preparedContent[] = [
                "id" => $item->id,
                "name" => $item->name,
                "description" => $item->description,
                "price" => $item->price,
                "rating" => $item->rating,
                "type" => $item->contentType->name,
                "currency" => $item->currencyType->name,
                "logo" => $url . $item->uploadPath . $item->logo,
                "categoryId" => $item->category_id,
                "producer" => $item->producer
            ];

        }

        return $preparedContent;
    }

    public function actionView($id)
    {
        $model = Content::find()->where(['id' => $id, 'active' => 1])->one();

        if(!$model) {
            throw new NotFoundHttpException('Content not found', 404);
        }

        $preparedModel = [
            "id" => $model->id,
            "name" => $model->name,
            "description" => $model->description,
            "price" => $model->price,
            "rating" => $model->rating,
            "type" => $model->contentType->name,
            "currency" => $model->currencyType->name,
            "logo" => Yii::$app->urlManager->createAbsoluteUrl(['/']).$model->uploadPath.$model->logo,
            "categoryId" => $model->category_id,
            "screenshots" => [],
            "video" => $model->video,
            "producer" => $model->producer
        ];

        return $preparedModel;
    }

    public function actionSubscribe($id)
    {
        $content = Content::find()->where(['id' => $id, 'active' => 1])->one();

        if(!$content) {
            throw new NotFoundHttpException('Content not found', 404);
        }

        $data = [
            "date" => 123,
            "status" => 0,
            "token" => Yii::$app->security->generateRandomString(),
            "content_id" => $id,
            "type_id" => $content->content_type_id,
            "currency_id" => $content->currency_type_id,
            "price" => $content->price,
            "customer_id" => null
        ];

        $model = new CustomersContent();
        $model->load($data);

        if($model->validate()) {
            $model->save();
            return ["id" => $id];
        } else {
            return $model->errors;
        }
    }

    public function actionUnsubscribe($id)
    {
        return ["id" => $id];
    }
}
