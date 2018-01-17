<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\imagine\Image;

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

        Image::thumbnail('uploads/coffe.jpg', 120, 120)->save(Yii::getAlias('uploads/thumb-test-image.jpg'), ['quality' => 50]);;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->updated_at = $model->created_at;

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
                $model->save();

                $data["errors"] = "";
                $data["success"] = 1;
                $data["redirectUrl"] = Url::to(['slider/update', 'id' => $model->id]);
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

        if(!$model) {
            throw new NotFoundHttpException('Slider item not found' ,404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $model->image = UploadedFile::getInstance($model, 'image');

            $data = [
                "message" => "Slider item was successfully updated"
            ];

            if ($model->validate()) {

                // все данные корректны
                $model->save();

                $model->image->saveAs('uploads/' . $model->image->baseName . '.' . $model->image->extension);

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
}