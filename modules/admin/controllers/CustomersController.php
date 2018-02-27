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
use app\models\CustomersContent;
use app\modules\admin\models\CustomersFilterForm;
use app\modules\admin\models\PerPageSettings;

class CustomersController extends \yii\web\Controller
{
    public $auth;
    public $roles_array = [];

    public function init()
    {
        if (!Yii::$app->user->can('viewCustomer')) {
            return $this->goHome();
        }

        parent::init();
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->can('viewCustomer')) {
            return $this->goHome();
        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'customers'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $sort = new Sort([
            'attributes' => [
                'id',
                'username',
                'email',
                'created_at',
                'updated_at',
                'active',
                'content'
            ],
            'defaultOrder' => ['created_at' => SORT_ASC],
        ]);

        $query = Customers::find();

        // search
        $search = new CustomersFilterForm();
        $search->load(Yii::$app->request->get());

        // apply filters
        if($search->validate()) {

            // active
            if($search->active && in_array($search->active, ["yes", "no"])) {
                $active = $search->active == "yes" ? 1:0;
                $query = $query->andWhere(["=", "active", $active]);
            }

            // dates
            if($search->created_date_begin) {
                $created_date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->created_date_begin." 00:00:00");
                $query = $query->andWhere([">=", "created_at", $created_date_begin->getTimestamp()]);

            }

            if($search->created_date_end) {
                $created_date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->created_date_end." 23:59:59");
                $query = $query->andWhere(["<=", "created_at", $created_date_end->getTimestamp()]);
            }

            if($search->updated_date_begin) {
                $updated_date_begin = DateTime::createFromFormat('m-d-Y H:i:s', $search->updated_date_begin." 00:00:00");
                $query = $query->andWhere([">=", "updated_at", $updated_date_begin->getTimestamp()]);
            }

            if($search->updated_date_end) {
                $updated_date_end = DateTime::createFromFormat('m-d-Y H:i:s', $search->updated_date_end." 23:59:59");
                $query = $query->andWhere(["<=", "updated_at", $updated_date_end->getTimestamp()]);
            }

            // email or name or id
            if($search->type && in_array($search->type, ["id", "email", "name"])) {
                if($search->type == "id" && $search->id) {
                    $query = $query->andWhere(["=", "id", $search->id]);
                }


                if($search->type == "email" && $search->email) {
                    $query = $query->andWhere(["like", "email", $search->email]);
                }

                if($search->type == "name" && $search->name) {
                    $query = $query->andWhere(["like", "username", $search->name]);
                }
            }


        } else {
            throw new NotFoundHttpException('Error submitting search query' ,404);
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $users = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            "users" => $users,
            "pages" => $pages,
            "search" => $search,
            "sort" => $sort
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('createCustomer')) {
            return $this->goHome();
        }

        $model = new Customers();
        $model->active = 1;
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
        if (!Yii::$app->user->can('updateCustomer')) {
            return $this->goHome();
        }

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
                $data["redirectUrl"] = Url::to(['customers/view', 'id' => $model->id]);
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

    public function actionView($id)
    {
        if (!Yii::$app->user->can('viewCustomer')) {
            return $this->goHome();
        }

        $model = Customers::find()->where(['id' => $id])->one();

        if(!$model) {
            throw new NotFoundHttpException('Customer not found' ,404);
        }

        return $this->render('view', [
            'title' => "View customer",
            'model' => $model,
        ]);
    }

    public function actionActivate()
    {
        if (!Yii::$app->user->can('updateCustomer')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Customers::find()->where(['id' => $id])->one();
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
        if (!Yii::$app->user->can('updateCustomer')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = Customers::find()->where(['id' => $id])->one();
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
        if (!Yii::$app->user->can('updateCustomer')) {
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
                $models[$id] = Customers::find()->where(['id' => $id])->one();
                if($models[$id]) {

                    $data[$id] = array(
                        "username" => $post["username"][$key],
                        "active" => $post["active"][$key],
                        "email" => $post["email"][$key]
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
        if (!Yii::$app->user->can('updateCustomer')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $model = Customers::find()->where(['id' => $id])->one();

            if (!$model) {
                throw new NotFoundHttpException('Customer not found', 404);
            } else {
                $model->load(array("active" => $model->active ? 0 : 1));
                $model->save(false);

                return json_encode(["status" => $model->active ? 1:-1],JSON_PRETTY_PRINT);
            }
        }
    }

    public function actionContent($id)
    {
        if (!Yii::$app->user->can('viewCustomer')) {
            return $this->goHome();
        }

        $model = Customers::find()->where(['id' => $id])->one();

        if (!$model) {
            throw new NotFoundHttpException('Customer not found', 404);
        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'customers'])->one();

        if(!$per_page_settings) {
            $page_size = 10;
        } else {
            $page_size = $per_page_settings->value;
        }

        $query = CustomersContent::find()->andWhere(["customer_id" => $id, "status" => 1]);

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $content = $query->orderBy("date DESC")->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('content', [
            "customer" => $model,
            "content" => $content,
            "pages" => $pages
        ]);
    }
}
