<?php

namespace app\modules\admin\controllers;

use Yii;
use Datetime;
use yii\data\Pagination;
use yii\data\Sort;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


use app\models\Category;
use app\models\SlypeeUser;
use app\models\Log;
use app\modules\admin\models\LogFilterForm;
use app\modules\admin\models\PerPageSettings;

class CategoryLogController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('viewCategoryLog')) {
            return $this->goHome();
        }

        $category = null;

        $per_page_settings = PerPageSettings::find()->where(['name' => 'category_log'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $search = new LogFilterForm();
        // search selects
        $categories = ArrayHelper::map(Log::find()->select('category.id, category.name')->joinWith("category")->orderBy("category.id")->distinct()->all(), "category.id", "category.name");
        $users = ArrayHelper::map(Log::find()->select('user_id, user.username')->joinWith("user")->orderBy("user_id")->distinct()->all(), "user_id", "user.username");
        $search->load(Yii::$app->request->get());

        $query = Log::find()->where(["object_type" => "category"])->joinWith("category")->joinWith("user")->joinWith("crudType");

        $sort = new Sort([
            'attributes' => [
                'id',
                'datetime',
                'user.username',
                'category.name',
                'crud_types.name'
            ],
            'defaultOrder' => ['datetime' => SORT_DESC],
        ]);

        // apply filters
        if($search->validate()) {
            if($search["category_id"]) {
                $query = $query->andWhere(["category_id" => $search["category_id"]]);
                $category = Category::find()->where(['id' => $search["category_id"]])->one();
            }

            if($search["user_id"]) {
                $query = $query->andWhere(["user_id" => $search["user_id"]]);
            }

            if($search->date_begin) {
                $date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->date_begin."00:00:00");
                $query = $query->andWhere([">=", "datetime", $date_begin->getTimestamp()]);
            }

            if($search->date_end) {
                $date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->date_end. " 23:59:59");
                $query = $query->andWhere(["<=", "datetime", $date_end->getTimestamp()]);
            }
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $logs = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "logs" => $logs,
            "search" => $search,
            "categories" => $categories,
            "users" => $users,
            "pages" => $pages,
            "sort" => $sort,
            "category" => $category
        ]);

    }
}
