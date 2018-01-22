<?php

namespace app\modules\admin\controllers;

use Yii;
use Datetime;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use app\models\Category;
use app\models\CategoryLog;

use app\modules\admin\models\CategoryLogFilterForm;
use app\modules\admin\models\PerPageSettings;

class CategoryLogController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
//        if (!Yii::$app->user->can('viewCategoryLog')) {
//            return $this->goHome();
//        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'category_log'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $query = CategoryLog::find();

        $search = new CategoryLogFilterForm();
        $search->load(Yii::$app->request->get());

        // apply filters
        if($search->validate()) {
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

        $logs = $query->orderBy("datetime desc")->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "logs" => $logs,
            "search" => $search,
            "pages" => $pages,
            "category" => null
        ]);

    }
}
