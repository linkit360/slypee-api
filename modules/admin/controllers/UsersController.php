<?php

namespace app\modules\admin\controllers;

use Yii;
use DateTime;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Sort;
use yii\data\Pagination;
use yii\helpers\Url;


use app\models\SlypeeUser;
use app\modules\admin\models\UsersFilterForm;
use app\modules\admin\models\PerPageSettings;

class UsersController extends \yii\web\Controller
{
    public $auth;
    public $roles_array = [];

    public function init()
    {
        if (!Yii::$app->user->can('viewUser')) {
            return $this->goHome();
        }

        $this->auth = Yii::$app->authManager;

        parent::init();
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->can('viewUser')) {
            return $this->goHome();
        }

        $auth = Yii::$app->authManager;

        $per_page_settings = PerPageSettings::find()->where(['name' => 'users'])->one();

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
            ],
            'defaultOrder' => ['created_at' => SORT_ASC],
        ]);

        // search
        $search = new UsersFilterForm();
        $search->load(Yii::$app->request->get());

        $query = SlypeeUser::find();

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

            // roles
            if($search->role) {
                $usersIds = Yii::$app->authManager->getUserIdsByRole($search->role);
                $query = $query->andWhere(["in", "id", $usersIds]);
            }
        }

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $users = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        if($users) {
            foreach ($users as $user) {
                $user->roleName = $user->id;
            }
        }

        $roles = ArrayHelper::map($auth->getRoles(), "name", "name");

        return $this->render('index', [
            "users" => $users,
            "search" => $search,
            "pages" => $pages,
            "sort" => $sort,
            "roles" => $roles
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('createUser')) {
            return $this->goHome();
        }

        $model = new SlypeeUser;
        $model->active = 1;
        $model->scenario = 'userCreate';

        $this->getRolesArray();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->updated_at = $model->created_at;

            $data = [
                "message" => "User was successfully added"
            ];

            if ($model->validate()) {

                // set password and auth key
                $model->setPassword($model->password);
                $model->generateAuthKey();
                $model->save();

                // set role
                $role = $this->auth->getRole($model->role);
                $this->auth->assign($role, $model->id);

                $data["errors"] = "";
                $data["success"] = 1;
                $data["redirectUrl"] = Url::to(['users/view', 'id' => $model->id]);
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
                'title' => "New user",
                'btn' => "Add user",
                'model' => $model,
                'roles' => $this->roles_array,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('updateUser')) {
            return $this->goHome();
        }

        $model = SlypeeUser::find()->where(['id' => $id])->one();
        $model->scenario = "userUpdate";

        if(!$model) {
            throw new NotFoundHttpException('Category not found' ,404);
        }

        $roles = $this->auth->getAssignments($model->id);

        // one role
        $old_role = '';
        if($roles) {
            reset($roles);
            $old_role = key($roles);

            $model->role = $old_role;
        }

        $this->getRolesArray();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "User was successfully updated"
            ];

            if ($model->validate()) {

                // все данные корректны

                // change password if necessary
                if($model->password) {
                    $model->setPassword($model->password);
                }

                // change role if necessary
                if($model->role != $old_role) {
                    $this->auth->revokeAll($model->id);
                    $role = $this->auth->getRole($model->role);
                    $this->auth->assign($role, $model->id);
                }

                $model->save();

                $data["errors"] = "";
                $data["success"] = 1;
                $data["unblock"] = 1;
                $data["redirectUrl"] = Url::to(['users/view', 'id' => $model->id]);
                return json_encode($data,JSON_PRETTY_PRINT);

            } else {
                // данные не корректны: $errors - массив содержащий сообщения об ошибках
                $data["errors"] = $model->errors;
                $data["success"] = 0;

                return json_encode($data,JSON_PRETTY_PRINT);
            }

        } else {

            return $this->render('create', [
                'title' => "Edit user",
                'btn' => "Save changes",
                'model' => $model,
                'roles' => $this->roles_array,
            ]);

        }
    }

    public function actionView($id)
    {
        if (!Yii::$app->user->can('viewUser')) {
            return $this->goHome();
        }

        $model = SlypeeUser::find()->where(['id' => $id])->one();

        $roles = $this->auth->getAssignments($model->id);

        // one role
        if($roles) {
            reset($roles);
            $old_role = key($roles);

            $model->role = $old_role;
        }

        if(!$model) {
            throw new NotFoundHttpException('User not found' ,404);
        }

        return $this->render('view', [
            'title' => "View user",
            'model' => $model,
        ]);
    }

    private function getRolesArray()
    {
        $roles = $this->auth->getRoles();
        $roles_array = [];

        foreach ($roles as $role) {
            $roles_array[$role->name] = $role->name;
        }

        $this->roles_array = $roles_array;
    }

    public function actionActivate()
    {
        if (!Yii::$app->user->can('updateUser')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = SlypeeUser::find()->where(['id' => $id])->one();
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
        if (!Yii::$app->user->can('updateUser')) {
            return $this->goHome();
        }

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $ids = $post["ids"];

            if(!$ids) {
                throw new NotFoundHttpException('Page not found' ,404);
            }

            foreach($ids as $id) {
                $model = SlypeeUser::find()->where(['id' => $id])->one();
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
        if (!Yii::$app->user->can('updateUser')) {
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
                $models[$id] = SlypeeUser::find()->where(['id' => $id])->one();
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
        if (!Yii::$app->user->can('updateUser')) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $model = SlypeeUser::find()->where(['id' => $id])->one();

            if (!$model) {
                throw new NotFoundHttpException('User not found', 404);
            } else {

                $model->load(array("active" => $model->active ? 0 : 1));
                $model->save(false);

                return json_encode(["status" => $model->active ? 1:-1],JSON_PRETTY_PRINT);
            }
        }
    }
}
