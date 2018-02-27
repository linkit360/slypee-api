<?php

namespace app\modules\api\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

use app\models\Customers;
use app\modules\api\models\LoginForm;
use app\modules\api\models\RecoveryForm;
use app\modules\api\models\RecoveryConfirmForm;

class CustomerController extends ActiveController
{
    public $modelClass = 'app\models\Customer';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        // get info
        if($request->isGet) {
            if(!Yii::$app->customer->isGuest) {
                # is customer logged
                return Yii::$app->customer->identity->prepareForApi();
            } else {
                # not logged
                return [];
            }
        }

        // registration
        if($request->isPost) {

            if(!Yii::$app->customer->isGuest) {
                throw new NotFoundHttpException('You are already logged in', 404);
            }

            $model = new Customers();
            $model->scenario = 'customerCreate';

            if ($model->load(Yii::$app->request->post())) {
                $model->content = 0;
                $model->active = 1;
                $model->created_at = time();
                $model->updated_at = $model->created_at;

                if ($model->validate()) {

                    // set password and auth key
                    $model->setPassword($model->password);
                    $model->generateAuthKey();
                    $model->save();

                    return $model->prepareForApi();
                } else {
                    return $model->errors;
                }
            }
        }

        // update profile
        if($request->isPut) {

            if(Yii::$app->customer->isGuest) {
                throw new NotFoundHttpException('You are not logged in', 404);
            }

            $id = Yii::$app->customer->identity->id;

            $model = Customers::find()->where(['id' => $id])->one();
            $model->scenario = "customerApiUpdate";

            $post = Yii::$app->request->post();

            if ($model->load($post)) {
                //$avatar = UploadedFile::getInstance($model, 'avatar');

                if(@$post["avatar"]) {
                    $model->saveAvatar();
                }

                if ($model->validate()) {

                    // change password if necessary
                    if ($model->password) {
                        $model->setPassword($model->password);
                    }


                    $model->save(false);

                    return $model->prepareForApi();

                } else {
                    return $model->errors;
                }
            }
        }

        throw new NotFoundHttpException('Method not allowed', 405);
    }

    public function actionSignin()
    {
        $post = Yii::$app->request->post();

        if(!Yii::$app->customer->isGuest) {
            throw new NotFoundHttpException('You are already logged in', 404);
        }

        $model = new LoginForm();

        if ($model->load($post)) {

            if($model->login()) {
                return $model->customer->prepareForApi();
            } else {
                return $model->errors;
            }
        }

        return [];
    }

    public function actionLogout()
    {
        if(Yii::$app->customer->isGuest) {
            throw new NotFoundHttpException('You are not logged in', 404);
        }

        $customer = Customers::findIdentity(Yii::$app->customer->identity->id);
        $customer->changeAuthToken();

        Yii::$app->customer->logout();
    }

    public function actionRecovery()
    {
        $post = Yii::$app->request->post();

        if(!Yii::$app->customer->isGuest) {
            throw new NotFoundHttpException('You are already logged in', 404);
        }

        $model = new RecoveryForm();

        if ($model->load($post)) {

            if($model->recovery()) {
                return [];
            } else {
                return $model->errors;
            }
        }
    }

    public function actionRecoveryConfirm()
    {
        $post = Yii::$app->request->post();

        if(!Yii::$app->customer->isGuest) {
            throw new NotFoundHttpException('You are already logged in', 404);
        }


        $model = new RecoveryConfirmForm();

        if ($model->load($post)) {

            if($model->recovery()) {
                return [];
            } else {
                return $model->errors;
            }
        }

    }
}