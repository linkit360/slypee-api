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
use app\models\Photos;
use app\models\ContentPhotos;
use app\models\RelatedContent;
use app\modules\admin\models\ContentFilterForm;
use app\modules\admin\models\PerPageSettings;

class ContentController extends \yii\web\Controller
{
    public function init()
    {
        if (!Yii::$app->user->can('viewContent')) {
            return $this->goHome();
        }

        parent::init();
    }

    public function actionTop()
    {
        if (!Yii::$app->user->can('updateContent')) {
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
        $session_per_page_settings = $session->get("content_per_page_settings");

        if(!$session_per_page_settings) {
            $per_page_settings = PerPageSettings::find()->where(['name' => 'content'])->one();

            $page_size = 10;

        } else {
            $page_size = $session_per_page_settings;
        }


        $query = Content::find();

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $content = $query->orderBy('priority')->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('top', [
            "content" => $content,
            "pages" => $pages,
            "page_size" => $page_size,
            "session_per_page_settings" => $session_per_page_settings
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
                'category.name',
                'content_types.name',
                'price',
                'currency_types.name',
                'rating',
                'created_at',
                'updated_at',
                'active',
            ],
            'defaultOrder' => ['rating' => SORT_DESC],
        ]);

        // search
        $search = new ContentFilterForm();
        $search->load(Yii::$app->request->get());
        // search selects
        $categories = ArrayHelper::map(Category::find()->orderBy("name")->all(), "id", "name");
        $currency_types = ArrayHelper::map(CurrencyTypes::find()->orderBy("name")->all(), "id", "name");
        $content_types = ArrayHelper::map(ContentTypes::find()->orderBy("name")->all(), "id", "name");

        $query = Content::find()->joinWith("category")->joinWith("contentType")->joinWith("currencyType");

        if ($search->validate()) {
            // active
            if ($search->active && in_array($search->active, ["yes", "no"])) {
                $active = $search->active == "yes" ? 1 : 0;
                $query = $query->andWhere(["=", "content.active", $active]);
            }

            // category
            if ($search->category) {
                $query = $query->andWhere(["=", "content.category_id", $search->category]);
            }

            if ($search->content_type) {
                $query = $query->andWhere(["=", "content.content_type_id", $search->content_type]);
            }

            if ($search->currency_type) {
                $query = $query->andWhere(["=", "content.currency_type_id", $search->currency_type]);
            }

            // dates
            if ($search->created_date_begin) {
                $created_date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->created_date_begin." 00:00:00");
                $query = $query->andWhere([">=", "content.created_at", $created_date_begin->getTimestamp()]);
            }

            if ($search->created_date_end) {
                $created_date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->created_date_end." 23:59:59");
                $query = $query->andWhere(["<=", "content.created_at", $created_date_end->getTimestamp()]);
            }

            if ($search->updated_date_begin) {
                $updated_date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->updated_date_begin." 00:00:00");
                $query = $query->andWhere([">=", "content.updated_at", $updated_date_begin->getTimestamp()]);
            }

            if ($search->updated_date_end) {
                $updated_date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->updated_date_end." 23:59:59");
                $query = $query->andWhere(["<=", "content.updated_at", $updated_date_end->getTimestamp()]);
            }

            // name or id
            if ($search->type && in_array($search->type, ["id", "name"])) {
                if ($search->type == "id" && $search->id) {
                    $query = $query->andWhere(["=", "content.id", $search->id]);
                }

                if ($search->type == "name" && $search->name) {
                    $query = $query->andWhere(["like", "content.name", $search->name]);
                }
            }

        } else {
            $errors = $search->errors;
            die(var_dump($errors));
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $content = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "content" => $content,
            "pages" => $pages,
            "search" => $search,
            "sort" => $sort,
            "categories" => $categories,
            "currency_types" => $currency_types,
            "content_types" => $content_types
        ]);

    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('createContent')) {
            return $this->goHome();
        }

        $model = new Content;
        $model->active = 1;
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

                $model->save();
                $model->saveLogo();
                $model->savePhotosIds();

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
                'model' => $model
            ]);
        }
    }

    public function actionView($id)
    {
        if (!Yii::$app->user->can('viewContent')) {
            return $this->goHome();
        }

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
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

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
                $model->save();

                if($logo) {
                    $model->saveLogo();
                }

                // new photos
                $model->savePhotosIds();

                // new related content
                $related = isset(Yii::$app->request->post()["related"]) ? Yii::$app->request->post()["related"]:null;

                if($related) {
                    foreach ($related as $r) {
                        $related_model = new RelatedContent();

                        $related_model->load([
                           "content_id_a" => $id,
                           "content_id_b" => $r
                        ]);

                        if($related_model->validate()) {
                            $related_model->save();
                        }
                    }
                }

                $data["errors"] = "";
                $data["success"] = 1;
                $data["unblock"] = 1;
                $data["redirectUrl"] = Url::to(['content/view', 'id' => $model->id]);
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
                'model' => $model
            ]);
        }
    }

    public function actionActivate()
    {
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Content::find()->where(['id' => $id])->one();
                if($model) {
                    $model->load(array("active" => Yii::$app->params["connection_type"] == "pgsql" ? true:1));
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
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Content::find()->where(['id' => $id])->one();
                if($model) {
                    $model->load(array("active" => Yii::$app->params["connection_type"] == "pgsql" ? false:0));
                    $model->save(false);
                }
            }

            return json_encode([],JSON_PRETTY_PRINT);

        } else {
            throw new NotFoundHttpException('Page not found' ,404);
        }
    }

    public function actionAjaxUpdate() {
        if (!Yii::$app->user->can('updateContent')) {
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
                $models[$id] = Content::find()->where(['id' => $id])->one();
                if($models[$id]) {

                    $data[$id] = array(
                        "name" => $post["name"][$key],
                        "active" => $post["active"][$key],
                        "price" => $post["price"][$key],
                        "rating" => $post["rating"][$key],
                        "category_id" => $post["category_id"][$key],
                        "currency_type_id" => $post["currency_type_id"][$key],
                        "content_type_id" => $post["content_type_id"][$key]
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
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $model = Content::find()->where(['id' => $id])->one();

            if (!$model) {
                throw new NotFoundHttpException('Content not found', 404);
            } else {
                $active = Yii::$app->params["connection_type"] == "pgsql" ? (intval($model->active) ? true:false) : intval($model->active);
                $active = !$active;

                $model->load(array("active" => $active));
                $model->save(false);

                return json_encode(["status" => $active ? 1:-1],JSON_PRETTY_PRINT);
            }
        }
    }

    public function actionPhoto()
    {
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

        $model = new Photos();

        if (Yii::$app->request->isPost) {

            $model->imageFiles = UploadedFile::getInstances($model, 'image');

            if ($ids = $model->upload()) {

                return json_encode(["ids" => $ids],JSON_PRETTY_PRINT);
            }
        }

        throw new NotFoundHttpException('Error', 404);
    }

    public function actionRemovePhoto() {
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

        $post = Yii::$app->request->post();
        $id = $post["id"];
        $content_id = $post["content_id"];

        $model = ContentPhotos::find()->where(['id' => $id, 'content_id' => $content_id])->one();

        if(!$model) {
            throw new NotFoundHttpException('Page not found' ,404);
        } else {
            $photo_id = $model->photo_id;
            $photo = Photos::find()->where(['id' => $photo_id])->one();
            unlink($photo->image);
            unlink($photo->thumbnail);
            $model->delete();
            $photo->delete();

            return json_encode(["status" => 1],JSON_PRETTY_PRINT);
        }
    }

    public function actionRemoveRelated() {
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

        $post = Yii::$app->request->post();
        $id = $post["id"];

        $model = RelatedContent::find()->where(['id' => $id])->one();

        if($model) {
            $model->delete();
        }

        return json_encode(["status" => 1],JSON_PRETTY_PRINT);
    }

    public function actionSearch()
    {
        if (!Yii::$app->user->can('updateContent')) {
            return $this->goHome();
        }

        if(!Yii::$app->request->isPost) {
            throw new NotFoundHttpException('Page not found' ,404);
        }

        $post = Yii::$app->request->post();

        // postgres -> ilike
        // postgress ilike --- mysql like
        $like = Yii::$app->params["connection_type"] == "pgsql" ? "ilike" : "like";
        $content = Content::find()->orWhere([$like, "name", $post["query"]])->orWhere(["id" => intval($post["query"])])->all();
        $preparedContent = [];

        foreach ($content as $c) {
            $preparedContent[] = [
                "id" => $c->id,
                "value" => $c->name,
                "logo" => "/".$c->uploadPath.$c->logo
            ];
        }

        return json_encode(["suggestions" => $preparedContent],JSON_PRETTY_PRINT);
    }
}
