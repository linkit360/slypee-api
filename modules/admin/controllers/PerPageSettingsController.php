<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\PerPageSettings;

class PerPageSettingsController extends Controller
{
    public function actionIndex()
    {
        $query = PerPageSettings::find();

        $settings = $query->orderBy("name")->all();

        return $this->render('index', [
            "settings" => $settings
        ]);
    }

    public function actionUpdate($id)
    {
        $model = PerPageSettings::find()->where(['id' => $id])->one();

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
                return json_encode($data,JSON_PRETTY_PRINT);

            } else {
                // данные не корректны: $errors - массив содержащий сообщения об ошибках
                $errors = $model->errors;
                $data["errors"] = $model->errors;
                $data["success"] = 0;

                return json_encode($data,JSON_PRETTY_PRINT);
            }

        } else {
            return $this->render('update', [
                'title' => "Pagination settings",
                'btn' => "Save changes",
                'model' => $model,
            ]);
        }
    }

}
