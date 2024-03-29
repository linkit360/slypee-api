<?php

namespace app\modules\admin\controllers;

use Yii;
use DateTime;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Sort;
use yii\data\Pagination;
use yii\helpers\Url;

use app\models\Category;
use app\modules\admin\models\CategoryForm;
use app\modules\admin\models\CategoryFilterForm;
use app\modules\admin\models\PerPageSettings;

/**
 * Default controller for the `admin` module
 */
class CategoryController extends Controller
{
    public function init()
    {
        if (!Yii::$app->user->can('viewCategory')) {
            return $this->goHome();
        }

        parent::init();
    }

    public function actionTop()
    {
        if (!Yii::$app->user->can('updateCategory')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $prev = $data["prev"];
            $current = $data["current"];
            $next = $data["next"];

            $current_model = Category::find()->where(['id' => $current])->one();

            if(!$current_model) {
                throw new NotFoundHttpException('Error' ,404);
            }

            $next_model = Category::find()->where(['id' => $next])->one();
            $prev_model = Category::find()->where(['id' => $prev])->one();

            if(!$prev_model && !$next_model) {
                throw new NotFoundHttpException('Error' ,404);
            }

            $current_priority = $current_model->priority;
            $prev_priority = $prev_model ? $prev_model->priority : 0;
            $next_priority = $next_model ? $next_model->priority : $prev_priority + 1; // sick

            if($current_priority > $prev_priority) {
                $items = Category::find()->andWhere(["<", "priority", $current_priority])->andWhere([">=", "priority", $next_priority])->all();
                if(!$items) {
                    return json_encode([],JSON_PRETTY_PRINT);
                }

                foreach ($items as $item) {
                    $item->updateCounters(["priority" => 1]);
                }

                $current_model->priority = $next_priority;


            } else {

                $items = Category::find()->andWhere(["<=", "priority", $prev_priority])->andWhere([">", "priority", $current_priority])->all();

                if(!$items) {
                    return json_encode([],JSON_PRETTY_PRINT);
                }

                foreach ($items as $item) {
                    $item->updateCounters(["priority" => -1]);
                }

                $current_model->priority = $prev_priority;
            }

            // TODO postgres issue -> set boolean field to null and model is not validated
            $current_model->save(false);

            $data = [0 => [
                "id" => $current_model->id,
                "priority" => $current_model->priority
            ]];

            foreach ($items as $item) {
                $data[] = [
                    "id" => $item->id,
                    "priority" => $item->priority
                ];
            }

            return json_encode($data,JSON_PRETTY_PRINT);
        }

        // from session
        $session = Yii::$app->session;
        $session_per_page_settings = $session->get("category_per_page_settings");

        if(!$session_per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $session_per_page_settings;
        }

        $query = Category::find();

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $categories = $query->orderBy('priority')->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('top', [
            "categories" => $categories,
            "pages" => $pages,
            "page_size" => $page_size,
            "session_per_page_settings" => $session_per_page_settings
        ]);
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->can('viewCategory')) {
            return $this->goHome();
        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'category'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $sort = new Sort([
            'attributes' => [
                'id',
                'age',
                'name',
                'priority',
                'created_at',
                'updated_at',
                'content',
                'active',
                'main_menu',
                'main_page'
            ],
            'defaultOrder' => ['priority' => SORT_DESC],
        ]);

        // search
        $search = new CategoryFilterForm();
        $search->load(Yii::$app->request->get());

        $query = Category::find();

        // apply filters
        if($search->validate()) {

            // active
            if($search->active && in_array($search->active, ["yes", "no"])) {
                $active = $search->active == "yes" ? 1:0;
                $query = $query->andWhere(["=", "active", $active]);
            }

            // dates
            if($search->created_date_begin) {
                $created_date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->created_date_begin." 00:00:00");
                $query = $query->andWhere([">=", "created_at", $created_date_begin->getTimestamp()]);

            }

            if($search->created_date_end) {
                $created_date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->created_date_end." 23:59:59");
                $query = $query->andWhere(["<=", "created_at", $created_date_end->getTimestamp()]);
            }

            if($search->updated_date_begin) {
                $updated_date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->updated_date_begin." 00:00:00");
                $query = $query->andWhere([">=", "updated_at", $updated_date_begin->getTimestamp()]);
            }

            if($search->updated_date_end) {
                $updated_date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->updated_date_end." 23:59:59");
                $query = $query->andWhere(["<=", "updated_at", $updated_date_end->getTimestamp()]);
            }

            // name or id
            if($search->type && in_array($search->type, ["id", "name"])) {
                if($search->type == "id" && $search->id) {
                    $query = $query->andWhere(["=", "id", $search->id]);
                }

                if($search->type == "name" && $search->name) {
                    $query = $query->andWhere(["like", "name", $search->name]);
                }
            }

        } else {
            $errors = $search->errors;
            die(var_dump($errors));
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $categories = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "categories" => $categories,
            "pages" => $pages,
            "search" => $search,
            "sort" => $sort
        ]);

    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('createCategory')) {
            return $this->goHome();
        }

        $model = new Category;
        $model->active = 1;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->content = 0;
            $model->created_at = time();
            $model->updated_at = $model->created_at;

            // initial priority
            $existed_priority = Category::find()->orderBy("priority DESC")->one();

            if($existed_priority) {
                $model->priority = $existed_priority->priority + 1;
            } else {
                $model->priority = 0;
            }

            $data = [
                "message" => "Category was successfully added"
            ];

            if ($model->validate()) {

                // все данные корректны
                $model->save();

                $data["errors"] = "";
                $data["success"] = 1;
                $data["redirectUrl"] = Url::to(['category/view', 'id' => $model->id]);
                return json_encode($data,JSON_PRETTY_PRINT);

            } else {
                // данные не корректны: $errors - массив содержащий сообщения об ошибках
                $errors = $model->errors;
                $data["errors"] = $model->errors;
                $data["success"] = 0;

                return json_encode($data,JSON_PRETTY_PRINT);
            }

        } else {

            return $this->render('create', [
                'title' => "New category",
                'btn' => "Add category",
                'model' => $model,
            ]);
        }
    }

    public function actionView($id)
    {
        if (!Yii::$app->user->can('viewCategory')) {
            return $this->goHome();
        }

        $model = Category::find()->where(['id' => $id])->one();

        if(!$model) {
            throw new NotFoundHttpException('Category not found' ,404);
        }

        return $this->render('view', [
            'title' => "View category",
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('updateCategory')) {
            return $this->goHome();
        }

        $model = Category::find()->where(['id' => $id])->one();

        if(!$model) {
            throw new NotFoundHttpException('Category not found' ,404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "Category was successfully updated"
            ];

            if ($model->validate()) {
                // все данные корректны
                $model->save();

                $data["errors"] = "";
                $data["success"] = 1;
                $data["unblock"] = 1;
                $data["redirectUrl"] = Url::to(['category/view', 'id' => $model->id]);
                return json_encode($data,JSON_PRETTY_PRINT);

            } else {
                // данные не корректны: $errors - массив содержащий сообщения об ошибках
                $data["errors"] = $model->errors;
                $data["success"] = 0;

                return json_encode($data,JSON_PRETTY_PRINT);
            }

        } else {
            return $this->render('create', [
                'title' => "Edit category",
                'btn' => "Save changes",
                'model' => $model,
            ]);
        }
    }

    public function actionActivate()
    {
        if (!Yii::$app->user->can('updateCategory')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Category::find()->where(['id' => $id])->one();
                if($model) {
                    $model->load(array("active" => 1));
                    $model->save(false);
                }
            }

            return json_encode([],JSON_PRETTY_PRINT);

        } else {
            throw new NotFoundHttpException('Page not found' ,404);
        }
    }

    public function actionDeactivate()
    {
        if (!Yii::$app->user->can('updateCategory')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Category::find()->where(['id' => $id])->one();
                if($model) {
                    $model->load(array("active" => 0));
                    $model->save(false);
                }
            }

            return json_encode([],JSON_PRETTY_PRINT);

        } else {
            throw new NotFoundHttpException('Page not found' ,404);
        }
    }

    public function actionAjaxUpdate() {
        if (!Yii::$app->user->can('updateCategory')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {

            $errors = [];
            $data = [];
            $models = [];
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $key => $id) {
                $models[$id] = Category::find()->where(['id' => $id])->one();
                if($models[$id]) {
                    $data[$id] = array(
                        "name" => $post["name"][$key],
                        "active" => $post["active"][$key],
                        "main_menu" => $post["main_menu"][$key],
                        "main_page" => $post["main_page"][$key]
                    );

                    $models[$id]->load($data[$id]);

                    // check errors
                    if (!$models[$id]->validate()) {
                        $errors[$id] = $models[$id]->errors;
                    }
                }
            }

            if(!$errors) {
                $response = array(
                    "success" => 1
                );

                foreach ($models as $index => $model) {
                    if($models[$index]) {
                        $models[$index]->save();
                    }
                }

            } else {
                $response = array(
                    "errors" => $errors,
                    "success" => 0
                );
            }

            return json_encode($response,JSON_PRETTY_PRINT);

        } else {
            throw new NotFoundHttpException('Page not found' ,404);
        }
    }

    public function actionReactivate($id)
    {
        if (!Yii::$app->user->can('updateCategory')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $model = Category::find()->where(['id' => $id])->one();

            if (!$model) {
                throw new NotFoundHttpException('Category not found', 404);
            } else {
                $model->load(array("active" => $model->active ? 0 : 1));
                $model->save(false);

                return json_encode(["status" => $model->active ? 1:-1],JSON_PRETTY_PRINT);
            }
        }
    }
}
