<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\web\Controller;

use app\models\Slider;

class SliderController extends \yii\web\Controller
{

    public function actionIndex()
    {
        $query = Slider::find();

        $slider = $query->orderBy("priority")->all();

        return $this->render('index', [
            "slider" => $slider
        ]);
    }

    public function actionCreate()
    {
        $model = new Slider();
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


                // все данные корректны
                $new_image_name = Yii::$app->security->generateRandomString() . '.' . $model->image->extension;
                $model->image->name = $new_image_name;
                $model->save();
                $model->image->saveAs($model->uploadPath.$new_image_name);

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

                // все данные корректны
                if($image) {
                    $new_image_name = Yii::$app->security->generateRandomString() . '.' . $model->image->extension;
                    $model->image->name = $new_image_name;
                }
                $model->save();
                if($image) {
                    $model->image->saveAs($model->uploadPath . $new_image_name);
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
            return $this->render('create', [
                'title' => "Edit slider item",
                'btn' => "Save changes",
                'model' => $model,
            ]);
        }
    }

    public function actionView($id)
    {
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
                $model = Slider::find()->where(['id' => $id])->one();
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
}
