<?php

namespace app\modules\admin\controllers;

use Yii;
use Datetime;
use yii\data\Pagination;
use yii\data\Sort;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use app\models\Log;
use app\models\SlypeeUser;
use app\modules\admin\models\LogFilterForm;
use app\modules\admin\models\UserLogFilterForm;
use app\modules\admin\models\PerPageSettings;

class LogController extends \yii\web\Controller
{
    public $types = array(
        'content' => [
            "model" => "app\models\Content",
            "alias" => "content",
            "table" => "content",
            "field" => "name",
            "title" => "Content %object% log",
            "logRule" => "viewContentLog"
        ],
        'category' => [
            "model" => "app\models\Category",
            "alias" => "category",
            "table" => "category",
            "field" => "name",
            "title" => "Category %object% log",
            "logRule" => "viewCategoryLog"
        ],
        'user' => [
            "model" => "app\models\SlypeeUser",
            "alias" => "slider",
            "table" => "slider",
            "field" => "username",
            "title" => "User %object% log",
            "logRule" => "viewUserLog"
        ],
        'customer' => [
            "model" => "app\models\Customers",
            "alias" => "customer",
            "table" => "customer",
            "field" => "username",
            "title" => "Customer %object% log",
            "logRule" => "viewCustomerLog"
        ],
        'slider' => [
            "model" => "app\models\Slider",
            "alias" => "slider",
            "table" => "slider",
            "field" => "title",
            "title" => "Slider %object% log",
            "logRule" => "viewSliderLog"
        ]
    );

    public function actionIndex($type)
    {

        if($type == "all") {
            return $this->actionIndexAll();
        }

        if(!array_key_exists($type, $this->types)) {
            throw new NotFoundHttpException('Not valid log type' ,404);
        }

        if (!Yii::$app->user->can($this->types[$type]["logRule"])) {
            return $this->goHome();
        }

        $object = null;

        $per_page_settings = PerPageSettings::find()->where(['name' => 'log'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $search = new LogFilterForm();
        $search->load(Yii::$app->request->get());

        $users = ArrayHelper::map(Log::find()->select('log.user_id, su.username as user_name')->joinWith("user")->orderBy("user_id")->distinct()->all(), "user_id", "user_name");
        $query = Log::find()->where(["object_type" => $type])->joinWith($this->types[$type]["alias"])->joinWith("user")->joinWith("crudType");

        $object_field = "{$this->types[$type]["alias"]}.{$this->types[$type]["field"]}";
        $sort = new Sort([
            'attributes' => [
                "id",
                "datetime",
                "user.username",
                $object_field,
                "crud_types.name",
            ],
            'defaultOrder' => ['datetime' => SORT_DESC],
        ]);

        // apply filters
        if($search->validate()) {
            if($search["object_id"]) {
                $query = $query->andWhere(["object_id" => $search["object_id"]]);

                // TODO якобы работает на php > 5.3 (всмысле можно короче записать, пока так оставлю)
                $model = $this->types[$type]["model"];
                $object_model = new $model();
                $object = $object_model->find()->where(['id' => $search["object_id"]])->one();
            }

            if($search["user_id"]) {
                $query = $query->andWhere(["user_id" => $search["user_id"]]);
            }

            if($search->date_begin) {
                $date_begin = DateTime::createFromFormat('m-d-Y', $search->date_begin);
                $query = $query->andWhere([">=", "datetime", $date_begin->getTimestamp()]);
            }

            if($search->date_end) {
                $date_end = DateTime::createFromFormat('m-d-Y', $search->date_end);
                $query = $query->andWhere(["<=", "datetime", $date_end->getTimestamp()]);
            }
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $logs = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "logs" => $logs,
            "search" => $search,
            //"objects" => $objects,
            "users" => $users,
            "pages" => $pages,
            "sort" => $sort,
            "object" => $object,
            "type" => $type,
            "title" => str_ireplace("%object%", $object ? $object[$this->types[$type]["field"]]: "", $this->types[$type]["title"]),
            "field" => $this->types[$type]["field"],
            "object_field" => $object_field
        ]);
    }

    function actionIndexAll()
    {
        if (!Yii::$app->user->can('viewUserLog')) {
            return $this->goHome();
        }

        $request = Yii::$app->request;
        $get = $request->get();

        $per_page_settings = PerPageSettings::find()->where(['name' => 'log'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $user_id = @$get["user_id"];
        $user = SlypeeUser::findOne(["id" => $user_id]);

        if(!$user) {
            throw new NotFoundHttpException('Not valid log type' ,404);
        }

        $search = new UserLogFilterForm();
        $search->load(Yii::$app->request->get());

        if(!$search->date_begin) {
            $search->date_begin = date("m-d-Y", strtotime("-1 month"));
        }

        // row sql or not
        $query = Log::find()->select('log.*, content.name as content_name, category.name as category_name, slider.title as slider_name, user.username as user_username, customers.username as customer_username')
            ->joinWith("content")
            ->joinWith("category")
            ->joinWith("customer")
            ->joinWith("slider")
            ->joinWith("user")
            ->joinWith("loggedUser")
            ->andWhere(["user_id" => $user_id]);

        if($search->validate()) {
            if($search->date_begin) {
                $date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->date_begin." 00:00:00");
                $query = $query->andWhere([">=", "datetime", $date_begin->getTimestamp()]);
            }

            if($search->date_end) {
                $date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->date_end." 23:59:59");
                $query = $query->andWhere(["<=", "datetime", $date_end->getTimestamp()]);
            }

            if($search->object_type) {
                $query = $query->andWhere(["=", "object_type", $search->object_type]);
            }
        } else {
            throw new NotFoundHttpException('Not valid search params' ,404);
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $logs = $query->orderBy("log.datetime DESC")->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('user', [
            "logs" => $logs,
            "search" => $search,
            "pages" => $pages,
            "user" => $user
        ]);

    }

}
