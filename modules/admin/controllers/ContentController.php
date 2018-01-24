<?php

namespace app\modules\admin\controllers;

use Yii;
use DateTime;
use yii\data\Sort;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

use app\models\Category;
use app\models\CurrencyTypes;
use app\models\ContentTypes;
use app\models\Content;
use app\modules\admin\models\CategoryFilterForm;
use app\modules\admin\models\PerPageSettings;

class ContentController extends \yii\web\Controller
{
    public function actionTop()
    {
        if (!Yii::$app->user->can('viewContent')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $prev = $data["prev"];
            $current = $data["current"];
            $next = $data["next"];

            $current_model = Content::find()->where(['id' => $current])->one();

            if(!$current_model) {
                throw new NotFoundHttpException('Error' ,404);
            }

            $next_model = Content::find()->where(['id' => $next])->one();
            $prev_model = Content::find()->where(['id' => $prev])->one();

            if(!$prev_model && !$next_model) {
                throw new NotFoundHttpException('Error' ,404);
            }

            $current_priority = $current_model->priority;
            $prev_priority = $prev_model ? $prev_model->priority : 0;
            $next_priority = $next_model ? $next_model->priority : $prev_priority + 1; // sick

            if($current_priority > $prev_priority) {
                $items = Content::find()->andWhere(["<", "priority", $current_priority])->andWhere([">=", "priority", $next_priority])->all();
                if(!$items) {
                    return json_encode([],JSON_PRETTY_PRINT);
                }
                foreach ($items as $item) {
                    $item->updateCounters(["priority" => 1]);
                }
                $current_model->priority = $next_priority;
            } else {
                $items = Content::find()->andWhere(["<=", "priority", $prev_priority])->andWhere([">", "priority", $current_priority])->all();
                if(!$items) {
                    return json_encode([],JSON_PRETTY_PRINT);
                }
                foreach ($items as $item) {
                    $item->updateCounters(["priority" => -1]);
                }
                $current_model->priority = $prev_priority;
            }

            $current_model->save();

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

        $per_page_settings = PerPageSettings::find()->where(['name' => 'content'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $query = Content::find();

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $content = $query->orderBy('priority')->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('top', [
            "content" => $content,
            "pages" => $pages,
        ]);
    }

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
        $model->scenario = 'contentCreate';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->updated_at = $model->created_at;
            $model->logo = UploadedFile::getInstance($model, 'logo');

            $data = [
                "message" => "Content was successfully added"
            ];

            $existed_priority = Content::find()->orderBy("priority DESC")->one();

            if($existed_priority) {
                $model->priority = $existed_priority->priority + 1;
            } else {
                $model->priority = 0;
            }

            if ($model->validate()) {

                $new_logo_name = Yii::$app->security->generateRandomString() . '.' . $model->logo->extension;
                $model->logo->name = $new_logo_name;
                $model->save();
                $model->logo->saveAs($model->uploadPath.$new_logo_name);

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
        //$model->scenario = 'contentUpdate';

        if(!$model) {
            throw new NotFoundHttpException('Content not found' ,404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $logo = UploadedFile::getInstance($model, 'logo');

            if($logo) {
                $model->logo = $logo;
            }

            $data = [
                "message" => "Content was successfully updated"
            ];

            if ($model->validate()) {
                // все данные корректны
                if($logo) {
                    $new_logo_name = Yii::$app->security->generateRandomString() . '.' . $model->logo->extension;
                    $model->logo->name = $new_logo_name;
                }
                $model->save();
                if($logo) {
                    $model->logo->saveAs($model->uploadPath . $new_logo_name);
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

    public function actionActivate()
    {
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Content::find()->where(['id' => $id])->one();
                if($model) {
                    $model->load(array("active" => 1));
                    $model->save();
                }
            }

            return json_encode([],JSON_PRETTY_PRINT);

        } else {
            throw new NotFoundHttpException('Page not found' ,404);
        }
    }

    public function actionDeactivate()
    {
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Content::find()->where(['id' => $id])->one();
                if($model) {
                    $model->load(array("active" => 0));
                    $model->save();
                }
            }

            return json_encode([],JSON_PRETTY_PRINT);

        } else {
            throw new NotFoundHttpException('Page not found' ,404);
        }
    }

    public function actionAjaxUpdate() {
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
                $models[$id] = Content::find()->where(['id' => $id])->one();
                if($models[$id]) {

                    $data[$id] = array(
                        "name" => $post["name"][$key],
                        "active" => $post["active"][$key],
                        "price" => $post["price"][$key],
                        "rating" => $post["rating"][$key]
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
        if (Yii::$app->request->isPost) {
            $model = Content::find()->where(['id' => $id])->one();

            if (!$model) {
                throw new NotFoundHttpException('Content not found', 404);
            } else {
                $model->load(array("active" => $model->active ? 0 : 1));
                $model->save();

                return json_encode(["status" => $model->active ? 1:-1],JSON_PRETTY_PRINT);
            }
        }
    }

}
