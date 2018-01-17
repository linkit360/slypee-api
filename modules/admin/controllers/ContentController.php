<?php

namespace app\modules\admin\controllers;

use Yii;
use DateTime;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Sort;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\models\Category;
use app\models\CurrencyTypes;
use app\models\ContentTypes;
use app\models\Content;
use app\modules\admin\models\CategoryFilterForm;
use app\modules\admin\models\PerPageSettings;

class ContentController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (!Yii::$app->user->can('viewContent')) {
            return $this->goHome();
        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'content'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $sort = new Sort([
            'attributes' => [
                'id',
                'name',
                'category',
                'type',
                'price',
                'currency',
                'rating',
                'created_at',
                'updated_at',
                'active',
            ],
            'defaultOrder' => ['rating' => SORT_DESC],
        ]);

        // search
        $search = new CategoryFilterForm();
        $search->load(Yii::$app->request->get());

        $query = Content::find();

        // apply filters
        if(false) {
            if ($search->validate()) {

                // active
                if ($search->active && in_array($search->active, ["yes", "no"])) {
                    $active = $search->active == "yes" ? 1 : 0;
                    $query = $query->andWhere(["=", "active", $active]);
                }

                // dates
                if ($search->created_date_begin) {
                    $created_date_begin = DateTime::createFromFormat('m-d-Y', $search->created_date_begin);
                    $query = $query->andWhere([">=", "created_at", $created_date_begin->getTimestamp()]);
                }

                if ($search->created_date_end) {
                    $created_date_end = DateTime::createFromFormat('m-d-Y', $search->created_date_end);
                    $query = $query->andWhere(["<=", "created_at", $created_date_end->getTimestamp()]);
                }

                if ($search->updated_date_begin) {
                    $updated_date_begin = DateTime::createFromFormat('m-d-Y', $search->updated_date_begin);
                    $query = $query->andWhere([">=", "updated_at", $updated_date_begin->getTimestamp()]);
                }

                if ($search->updated_date_end) {
                    $updated_date_end = DateTime::createFromFormat('m-d-Y', $search->updated_date_end);
                    $query = $query->andWhere(["<=", "updated_at", $updated_date_end->getTimestamp()]);
                }

                // name or id
                if ($search->type && in_array($search->type, ["id", "name"])) {
                    if ($search->type == "id" && $search->id) {
                        $query = $query->andWhere(["=", "id", $search->id]);
                    }

                    if ($search->type == "name" && $search->name) {
                        $query = $query->andWhere(["like", "name", $search->name]);
                    }
                }


            } else {
                $errors = $search->errors;
                die(var_dump($errors));
            }
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $content = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "content" => $content,
            "pages" => $pages,
            "search" => $search,
            "sort" => $sort
        ]);

    }

    public function actionCreate()
    {
        $model = new Content;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->updated_at = $model->created_at;

            $data = [
                "message" => "Content was successfully added"
            ];

            if ($model->validate()) {
                $model->logo = "test.jpg";

                // все данные корректны
                $model->save();

                // update category counter
                $category_id = Yii::$app->request->post()["category_id"];
                $category_model = Category::find()->where(['id' => $category_id])->one();
                $category_model->updateCounters(["content" => 1]);


                $data["errors"] = "";
                $data["success"] = 1;
                $data["redirectUrl"] = Url::to(['content/view', 'id' => $model->id]);
                return json_encode($data,JSON_PRETTY_PRINT);

            } else {
                // данные не корректны: $errors - массив содержащий сообщения об ошибках
                $errors = $model->errors;
                $data["errors"] = $model->errors;
                $data["success"] = 0;

                return json_encode($data,JSON_PRETTY_PRINT);
            }

        } else {

            $categories = ArrayHelper::map(Category::find()->orderBy("name")->all(), "id", "name");
            $currency_types = ArrayHelper::map(CurrencyTypes::find()->orderBy("name")->all(), "id", "name");
            $content_types = ArrayHelper::map(ContentTypes::find()->orderBy("name")->all(), "id", "name");

            return $this->render('create', [
                'categories' => $categories,
                'currency_types' => $currency_types,
                'content_types' => $content_types,
                'title' => "New content",
                'btn' => "Add content",
                'model' => $model,
            ]);
        }
    }

    public function actionView($id)
    {
        $model = Content::find()->where(['id' => $id])->one();

        if(!$model) {
            throw new NotFoundHttpException('Content not found' ,404);
        }

        return $this->render('view', [
            'title' => "View content",
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Content::find()->where(['id' => $id])->one();
        $old_category_id = $model->category_id;

        if(!$model) {
            throw new NotFoundHttpException('Content not found' ,404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "Content was successfully updated"
            ];

            if ($model->validate()) {
                // все данные корректны
                $model->save();

                // update category counter
                $category_id = Yii::$app->request->post()["category_id"];

                if($old_category_id != $category_id) {
                    $category_model = Category::find()->where(['id' => $category_id])->one();
                    $category_model->updateCounters(["content" => 1]);

                    $old_category_model = Category::find()->where(['id' => $old_category_id])->one();
                    $old_category_model->updateCounters(["content" => -1]);
                }

                $data["errors"] = "";
                $data["success"] = 1;
                $data["unblock"] = 1;
                return json_encode($data,JSON_PRETTY_PRINT);

            } else {
                // данные не корректны: $errors - массив содержащий сообщения об ошибках
                $data["errors"] = $model->errors;
                $data["success"] = 0;

                return json_encode($data,JSON_PRETTY_PRINT);
            }

        } else {

            $categories = ArrayHelper::map(Category::find()->orderBy("name")->all(), "id", "name");
            $currency_types = ArrayHelper::map(CurrencyTypes::find()->orderBy("name")->all(), "id", "name");
            $content_types = ArrayHelper::map(ContentTypes::find()->orderBy("name")->all(), "id", "name");

            return $this->render('create', [
                'categories' => $categories,
                'currency_types' => $currency_types,
                'content_types' => $content_types,
                'title' => "Edit content",
                'btn' => "Save changes",
                'model' => $model,
            ]);
        }
    }

}
