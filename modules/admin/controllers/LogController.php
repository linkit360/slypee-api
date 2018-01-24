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
use app\modules\admin\models\LogFilterForm;
use app\modules\admin\models\PerPageSettings;

class LogController extends \yii\web\Controller
{
    public $types = array(
        'content' => [
            "model" => "app\models\Content",
            "alias" => "content",
            "table" => "content",
            "field" => "name"
        ],
        'category' => [
            "model" => "app\models\Category",
            "alias" => "category",
            "table" => "category",
            "field" => "name"
        ],
        'user' => [
            "model" => "app\models\SlypeeUser",
            "alias" => "slider",
            "table" => "slider",
            "field" => "username"
        ],
        'customer' => [
            "model" => "app\models\Customer",
            "alias" => "customer",
            "table" => "customer",
            "field" => "username"
        ],
        'slider' => [
            "model" => "app\models\Slider",
            "alias" => "slider",
            "table" => "slider",
            "field" => "title"
        ]
    );

    public function actionIndex($type)
    {
        if(!array_key_exists($type, $this->types)) {
            throw new NotFoundHttpException('Not valid log type' ,400);
        }

        //        if (!Yii::$app->user->can('viewCategoryLog')) {
//            return $this->goHome();
//        }

        $object = null;

        $per_page_settings = PerPageSettings::find()->where(['name' => 'log'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $search = new LogFilterForm();
        $search->load(Yii::$app->request->get());

        $users = ArrayHelper::map(Log::find()->select('user_id, user.username')->joinWith("user")->orderBy("user_id")->distinct()->all(), "user_id", "user.username");
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
            "field" => $this->types[$type]["field"],
            "object_field" => $object_field
        ]);
    }

}
