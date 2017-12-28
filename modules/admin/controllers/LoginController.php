<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\LoginForm;

/**
 * Default controller for the `admin` module
 */
class LoginController extends Controller
{
    public function init()
    {
        parent::init();

        $this->layout = 'login';
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "You was successfully logged in"
            ];

            if($model->login()) {

                $data["errors"] = "";
                $data["success"] = 1;

            } else {

                $data["errors"] = $model->errors;
                $data["success"] = 0;

            }

            return json_encode($data,JSON_PRETTY_PRINT);

        }

        return $this->render('login', [
            'model' => $model,
        ]);

    }
}
