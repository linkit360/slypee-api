<?php

namespace app\modules\api\controllers;


use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

use app\models\Category;
use app\models\Content;
use app\models\CustomersContent;
use app\models\Customers;

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
        $headers = Yii::$app->request->headers;

        $query = Content::find()->joinWith("category")->andWhere(['content.active' => 1, 'category.active' => 1]);

        if(isset($headers["slypee-content-category"]) && $headers["slypee-content-category"]) {
            $query = $query->andWhere(["=", "category_id", $headers["slypee-content-category"]]);
        }

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

        $offset = 0;
        $limit = 10;
        if(isset($headers["slypee-content-pagination-start"])) {
            $offset = intval($headers["slypee-content-pagination-start"]);
        }
        if(isset($headers["slypee-content-pagination-limit"])) {
            $limit = intval($headers["slypee-content-pagination-limit"]);
        }

        $content = $query->orderBy("priority")->offset($offset)->limit($limit)->all();

        $preparedContent = [];

        foreach($content as $item) {
            $preparedContent[] = $item->prepareForListApi();
        }

        return $preparedContent;
    }

    public function actionSearch()
    {
        $headers = Yii::$app->request->headers;

        $query = Content::find()->joinWith("category")->andWhere(['content.active' => 1, 'category.active' => 1]);

        // search
        if(isset($headers["slypee-content-query"])) {
            $searchQuery = rawurldecode($headers["slypee-content-query"]);

            if(!$searchQuery) {
                throw new NotFoundHttpException('Search query is too short', 404);
            }

            // postgress ilike --- mysql like
            $like = Yii::$app->params["connection_type"] == "pgsql" ? "ilike" : "like";

            $query = $query->andWhere([$like, "content.name", $searchQuery]);
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

        $content = $query->orderBy("content.priority")->offset($offset)->limit($limit)->all();

        $preparedContent = [];

        foreach($content as $item) {
            $preparedContent[] = $item->prepareForListApi();
        }

        return $preparedContent;
    }

    public function actionCustomer()
    {
        if(Yii::$app->customer->isGuest) {
            throw new NotFoundHttpException('You are not logged in', 404);
        }

        $orderBy = "";
        $ordering = [
            "name" => "content.name ASC",
            "-name" => "content.name DESC",
            "date" => "date ASC",
            "-date" => "date DESC"
        ];

        $headers = Yii::$app->request->headers;

        $query = CustomersContent::find()->joinWith("content")->andWhere(["status" => 1, "customer_id" => Yii::$app->customer->identity->id]);

        if(isset($headers["slypee-content-type"])) {
            $type = $headers["slypee-content-type"];

            if($type == "subscription") {
                $query = $query->andWhere(["!=", 'content_type_id', 1]);
            }

            if($type == "single") {
                $query = $query->andWhere(["=", 'content_type_id', 1]);
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
            $orderBy = $ordering["-date"];
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
            $preparedContent[] = $item->prepareForCustomerApi();
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
            "-top" => "priority DESC",
            "date" => "created_at DESC"
        ];

        $headers = Yii::$app->request->headers;

        $category = Category::find()->andWhere(["active" => 1, "id" => $id])->one();

        if(!$category) {
            throw new NotFoundHttpException('Page not found', 404);
        }

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
        return [];
        // not necessary method

        $preparedContent = [];
        $content = Content::find()->where(['active' => 1])->all();

        foreach($content as $item) {
            $preparedContent[] = $item->prepareForListApi();
        }

        return $preparedContent;
    }

    public function actionView($id)
    {
        $model = Content::find()->joinWith("category")->andWhere(['content.id' => $id, 'content.active' => 1, 'category.active' => 1])->one();

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

        // check category

        $category = Category::find()->where(['id' => $content->category_id, 'active' => 1])->one();

        if(!$category) {
            throw new NotFoundHttpException('Content not found', 404);
        }

        $data = [
            "date" => time(),
            "status" => 0,
            "token" => Yii::$app->security->generateRandomString(),
            "content_id" => $id,
            "type_id" => $content->content_type_id,
            "currency_id" => $content->currency_type_id,
            "price" => $content->price,
            "customer_id" => Yii::$app->customer->isGuest ? null:Yii::$app->customer->identity->id
        ];

        $model = new CustomersContent();
        $model->load($data);

        if($model->validate()) {
            $model->save();

            // redirect to site
            $type = $content->contentType->name;
            $price = $content->price;
            $token = $model->token;
            $success_url = "https://slypee.snpdev.ru/api/content/subscribe-success?token=$token";
            $error_url = "https://slypee.snpdev.ru/api/content/subscribe-error?token=$token";

            return ["redirectUrl" => "http://portal.api.linkit360.ru/v-1/subscribe?type=$type&price=$price&cid=$id&success_url=$success_url&error_url=$error_url"];

        } else {
            return $model->errors;
        }
    }

    public function actionUnsubscribe($id)
    {
        $content = Content::find()->where(['id' => $id, 'active' => 1])->one();

        if(!$content) {
            throw new NotFoundHttpException('Content not found', 404);
        }

        // check category

        $category = Category::find()->where(['id' => $content->category_id, 'active' => 1])->one();

        if(!$category) {
            throw new NotFoundHttpException('Content not found', 404);
        }


        // if not logged don't change data
        $token = '';
        if(!Yii::$app->customer->isGuest) {
            $customer = Customers::findIdentity(Yii::$app->customer->identity->id);
            $customer_content = CustomersContent::find()->andWhere(["content_id" => $id, "customer_id" => Yii::$app->customer->identity->id])->one();
            if($customer_content) {
                $token = $customer_content->token;
            }
        }

        $type = $content->contentType->name;
        $price = $content->price;
        $success_url = "https://slypee.snpdev.ru/api/content/unsubscribe-success?token=$token";
        $error_url = "https://slypee.snpdev.ru/api/content/unsubscribe-error?token=$token";
        return ["redirectUrl" => "http://portal.api.linkit360.ru/v-1/unsubscribe?type=$type&price=$price&cid=$id&success_url=$success_url&error_url=$error_url"];
    }

    public function actionSubscribeSuccess()
    {
        $get = Yii::$app->request->get();

        $token = isset($get["token"]) && $get["token"] ? $get["token"]:"";

        if(!$token)
        {
            throw new NotFoundHttpException('Page not found', 404);
        }

        $model = CustomersContent::find()->andWhere(["token" => $token])->one();

        if($model) {
            $model->status = 1;
            $model->save();

            if($model->customer) {
                $model->customer->updateCounters(["content" => 1]);
            }

            return $this->redirect("/subscribe/success");
        }

        throw new NotFoundHttpException('Page not found', 404);
    }

    public function actionSubscribeError()
    {
        $get = Yii::$app->request->get();

        $token = isset($get["token"]) && $get["token"] ? $get["token"]:"";

        if(!$token)
        {
            throw new NotFoundHttpException('Page not found', 404);
        }

        $model = CustomersContent::find()->andWhere(["token" => $token])->one();

        if($model) {
            return $this->redirect("/subscribe/error");
        }

        throw new NotFoundHttpException('Page not found', 404);
    }

    public function actionUnsubscribeSuccess()
    {
        $get = Yii::$app->request->get();

        $token = isset($get["token"]) && $get["token"] ? $get["token"]:"";

        // unsubscribe customer
        if($token) {
            $model = CustomersContent::find()->andWhere(["token" => $token])->one();

            if($model) {
                $model->status = 0;
                $model->save();

                if($model->customer) {
                    $model->customer->updateCounters(["content" => -1]);
                }
            }
        }

        return $this->redirect("/unsubscribe/success");
    }

    public function actionUnsubscribeError()
    {
        return $this->redirect("/unsubscribe/error");
    }
}
