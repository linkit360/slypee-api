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

    public function actionSearch()
    {
        $headers = Yii::$app->request->headers;

        $query = Content::find()->where(['active' => 1]);

        // search
        if(isset($headers["slypee-content-query"])) {
            $searchQuery = $headers["slypee-content-query"];

            if(!$searchQuery) {
                throw new NotFoundHttpException('Search query is too short', 404);
            }

            // postgress ilike --- mysql like
            $query = $query->andWhere(["ilike", "name", $searchQuery]);
        } else {
            throw new NotFoundHttpException('Search query is too short', 404);
        }

        // pagination!
        $offset = 0;
        $limit = 10;
        if(isset($headers["slypee-content-pagination-start"])) {
            $offset = intval($headers["slypee-content-pagination-start"]);
        }
        if(isset($headers["slypee-content-pagination-limit"])) {
            $limit = intval($headers["slypee-content-pagination-limit"]);
        }

        $content = $query->orderBy("priority")->all();

        $preparedContent = [];

        foreach($content as $item) {
            $preparedContent[] = $item->prepareForListApi();
        }

        return $preparedContent;
    }

    public function  actionCategory($id)
    {
        $orderBy = "";
        $ordering = [
            "rating" => "rating DESC",
            "-rating" => "rating ASC",
            "top" => "priority ASC",
            "-top" => "priority DESC"
        ];

        $headers = Yii::$app->request->headers;

        $query = Content::find();

        // filters
        $query = $query->andWhere(["=", "active", 1])->andWhere(["=", 'category_id', $id]);

        if(isset($headers["slypee-content-type"])) {
            $type = $headers["slypee-content-type"];
            if($type == "free") {
                $query = $query->andWhere(["=", "price", 0]);
            }

            if($type == "subscription") {
                $query = $query->andWhere([">", "price", 0])->andWhere(["!=", 'content_type_id', 1]);
            }

            if($type == "single") {
                $query = $query->andWhere([">", "price", 0])->andWhere(["=", 'content_type_id', 1]);
            }
        }

        // ordering
        if(isset($headers["slypee-content-ordering"])) {
            $order = $headers["slypee-content-ordering"];
            if(array_key_exists($order, $ordering)) {
                $orderBy = $ordering[$order];
            }
        }

        // initial ordering
        if(!$orderBy) {
            $orderBy = $ordering["rating"];
        }

        // pagination!
        $offset = 0;
        $limit = 10;
        if(isset($headers["slypee-content-pagination-start"])) {
            $offset = intval($headers["slypee-content-pagination-start"]);
        }
        if(isset($headers["slypee-content-pagination-limit"])) {
            $limit = intval($headers["slypee-content-pagination-limit"]);
        }

        $content = $query->orderBy($orderBy)->offset($offset)->limit($limit)->all();

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
