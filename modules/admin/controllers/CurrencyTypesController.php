<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\models\CurrencyTypes;
use yii\helpers\Url;

class CurrencyTypesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query = CurrencyTypes::find();

        $types = $query->orderBy("name")->all();

        return $this->render('index', [
            "types" => $types
        ]);
    }

    public function actionCreate()
    {
        $model = new CurrencyTypes();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "Currency type was successfully added"
            ];

            if ($model->validate()) {

                // все данные корректны
                $model->save();

                $data["errors"] = "";
                $data["success"] = 1;
                $data["redirectUrl"] = Url::to(['currency-types/update', 'id' => $model->id]);
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
                'title' => "New currency type",
                'btn' => "Add currency type",
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = CurrencyTypes::find()->where(['id' => $id])->one();

        if(!$model) {
            throw new NotFoundHttpException('Item not found' ,404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "Value was successfully updated"
            ];

            if ($model->validate()) {
                // все данные корректны

                $model->save();
                $data["errors"] = "";
                $data["success"] = 1;
                $data["unblock"] = 1;
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
                'title' => "Currency types",
                'btn' => "Save changes",
                'model' => $model,
            ]);
        }
    }
}
