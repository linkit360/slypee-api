<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\data\Pagination;

use app\modules\admin\models\PerPageSettings;
use app\modules\admin\models\SliderFilterForm;
use app\models\Slider;

class SliderController extends \yii\web\Controller
{
    public function init()
    {
        if (!Yii::$app->user->can('viewSlider')) {
            return $this->goHome();
        }

        parent::init();
    }


    public function actionTop()
    {
        if (!Yii::$app->user->can('updateSlider')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            $prev = $data["prev"];
            $current = $data["current"];
            $next = $data["next"];

            $current_model = Slider::find()->where(['id' => $current])->one();

            if(!$current_model) {
                throw new NotFoundHttpException('Error' ,404);
            }

            $next_model = Slider::find()->where(['id' => $next])->one();
            $prev_model = Slider::find()->where(['id' => $prev])->one();

            if(!$prev_model && !$next_model) {
                throw new NotFoundHttpException('Error' ,404);
            }

            $current_priority = $current_model->priority;
            $prev_priority = $prev_model ? $prev_model->priority : 0;
            $next_priority = $next_model ? $next_model->priority : $prev_priority + 1; // sick

            if($current_priority > $prev_priority) {
                $items = Slider::find()->andWhere(["<", "priority", $current_priority])->andWhere([">=", "priority", $next_priority])->all();
                if(!$items) {
                    return json_encode([],JSON_PRETTY_PRINT);
                }
                foreach ($items as $item) {
                    $item->updateCounters(["priority" => 1]);
                }
                $current_model->priority = $next_priority;
            } else {
                $items = Slider::find()->andWhere(["<=", "priority", $prev_priority])->andWhere([">", "priority", $current_priority])->all();
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

        throw new NotFoundHttpException('Page not found' ,404);
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->can('viewSlider')) {
            return $this->goHome();
        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'slider'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        // search
        $search = new SliderFilterForm();
        $search->load(Yii::$app->request->get());

        $query = Slider::find();

        // apply filters
        if($search->validate()) {
            // active
            if($search->active && in_array($search->active, ["yes", "no"])) {
                $active = $search->active == "yes" ? 1:0;
                $query = $query->andWhere(["=", "active", $active]);
            }

            if($search->type && in_array($search->type, ["id", "email", "name"])) {
                if($search->type == "id" && $search->id) {
                    $query = $query->andWhere(["=", "id", $search->id]);
                }


                if($search->type == "name" && $search->name) {
                    $query = $query->andWhere(["like", "username", $search->name]);
                }
            }
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $slider = $query->orderBy("id DESC")->offset($pages->offset)->limit($pages->limit)->all();

        $slider = $query->orderBy("priority")->all();

        return $this->render('index', [
            "slider" => $slider,
            "search" => $search,
            "pages" => $pages,
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('createSlider')) {
            return $this->goHome();
        }

        $model = new Slider();
        $model->active = 1;
        $model->scenario = 'sliderCreate';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->updated_at = $model->created_at;
            $model->image = UploadedFile::getInstance($model, 'image');

            // initial priority
            $existed_priority = Slider::find()->orderBy("priority DESC")->one();

            if($existed_priority) {
                $model->priority = $existed_priority->priority + 1;
            } else {
                $model->priority = 0;
            }

            $data = [
                "message" => "Slider item was successfully added"
            ];

            if ($model->validate()) {

                $model->save();
                $model->saveImage();

                $data["errors"] = "";
                $data["success"] = 1;
                $data["redirectUrl"] = Url::to(['slider/view', 'id' => $model->id]);
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
                'title' => "New slider item",
                'btn' => "Add slider item",
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('updateSlider')) {
            return $this->goHome();
        }

        $model = Slider::find()->where(['id' => $id])->one();
        //$model->scenario = 'sliderUpdate';

        if(!$model) {
            throw new NotFoundHttpException('Slider item not found' ,404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $image = UploadedFile::getInstance($model, 'image');

            if($image) {
                $model->image = $image;
            }

            $data = [
                "message" => "Slider item was successfully updated"
            ];

            if ($model->validate()) {

                $model->save();

                if($image) {
                    $model->saveImage();
                }

                $data["errors"] = "";
                $data["success"] = 1;
                $data["unblock"] = 1;
                $data["redirectUrl"] = Url::to(['slider/view', 'id' => $model->id]);
                return json_encode($data,JSON_PRETTY_PRINT);

            } else {
                // данные не корректны: $errors - массив содержащий сообщения об ошибках
                $data["errors"] = $model->errors;
                $data["success"] = 0;

                return json_encode($data,JSON_PRETTY_PRINT);
            }

        } else {
            return $this->render('create', [
                'title' => "Edit slider item",
                'btn' => "Save changes",
                'model' => $model,
            ]);
        }
    }

    public function actionView($id)
    {
        if (!Yii::$app->user->can('viewSlider')) {
            return $this->goHome();
        }

        $model = Slider::find()->where(['id' => $id])->one();

        if(!$model) {
            throw new NotFoundHttpException('Slider item not found' ,404);
        }

        return $this->render('view', [
            'title' => "View slider item",
            'model' => $model,
        ]);
    }

    public function actionActivate()
    {
        if (!Yii::$app->user->can('updateSlider')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Slider::find()->where(['id' => $id])->one();
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
        if (!Yii::$app->user->can('updateSlider')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Slider::find()->where(['id' => $id])->one();
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
        if (!Yii::$app->user->can('updateSlider')) {
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
                $models[$id] = Slider::find()->where(['id' => $id])->one();
                if($models[$id]) {
                    $data[$id] = array(
                        "title" => $post["title"][$key],
                        "active" => $post["active"][$key],
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
        if (!Yii::$app->user->can('updateSlider')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $model = Slider::find()->where(['id' => $id])->one();

            if (!$model) {
                throw new NotFoundHttpException('Slider item not found', 404);
            } else {
                $model->load(array("active" => $model->active ? 0 : 1));
                $model->save(false);

                return json_encode(["status" => $model->active ? 1:-1],JSON_PRETTY_PRINT);
            }
        }
    }
}
