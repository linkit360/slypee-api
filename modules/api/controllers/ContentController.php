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

    public function  actionTop()
    {
        $content = Content::find()->where(['active' => 1])->orderBy("priority")->all();

        $preparedContent = [];

        foreach($content as $item) {
            $preparedContent[] = $item->prepareForListApi();
        }

        return $preparedContent;
    }

    public function  actionCategory($id)
    {
//        $headers = Yii::$app->request->headers;
//
//        return $headers;

        $content = Content::find()->where(['active' => 1, 'category_id' => $id])->all();

        $preparedContent = [];

        foreach($content as $item) {
            $preparedContent[] = $item->prepareForListApi();
        }

        return $preparedContent;
    }

    public function actionIndex()
    {
        $preparedContent = [];
        $content = Content::find()->where(['active' => 1])->all();

        foreach($content as $item) {
            $preparedContent[] = $item->prepareForListApi();
        }

        return $preparedContent;
    }

    public function actionView($id)
    {
        $model = Content::find()->where(['id' => $id, 'active' => 1])->one();

        if(!$model) {
            throw new NotFoundHttpException('Content not found', 404);
        }

        return $model->prepareForApi();
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
