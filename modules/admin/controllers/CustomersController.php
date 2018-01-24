<?php

namespace app\modules\admin\controllers;

use Yii;
use DateTime;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Sort;
use yii\data\Pagination;
use yii\helpers\Url;

use app\models\Customers;
use app\modules\admin\models\PerPageSettings;

class CustomersController extends \yii\web\Controller
{
    public $auth;
    public $roles_array = [];

    public function init()
    {
        parent::init();
    }

    public function actionIndex()
    {
//        if (!Yii::$app->user->can('viewCategory')) {
//            return $this->goHome();
//        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'customers'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $sort = new Sort([
            'attributes' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
                'active',
            ],
            'defaultOrder' => ['created_at' => SORT_ASC],
        ]);

        $query = Customers::find();

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $users = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "users" => $users,
            "pages" => $pages,
            "sort" => $sort
        ]);
    }

    public function actionCreate()
    {
        $model = new Customers;
        $model->scenario = 'customerCreate';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->content = 0;
            $model->created_at = time();
            $model->updated_at = $model->created_at;

            $data = [
                "message" => "Customer was successfully added"
            ];

            if ($model->validate()) {

                // set password and auth key
                $model->setPassword($model->password);
                $model->generateAuthKey();
                $model->save();

                $data["errors"] = "";
                $data["success"] = 1;
                $data["redirectUrl"] = Url::to(['customers/view', 'id' => $model->id]);
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
                'title' => "New customer",
                'btn' => "Add customer",
                'model' => $model
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = Customers::find()->where(['id' => $id])->one();
        $model->scenario = "customerUpdate";

        if(!$model) {
            throw new NotFoundHttpException('Customer not found' ,404);
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "Customer was successfully updated"
            ];

            if ($model->validate()) {

                // change password if necessary
                if($model->password) {
                    $model->setPassword($model->password);
                }

                $model->save();

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
                'title' => "Edit customer",
                'btn' => "Save changes",
                'model' => $model
            ]);

        }
    }

    public function actionView()
    {

    }
}
