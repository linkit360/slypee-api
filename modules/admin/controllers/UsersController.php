<?php

namespace app\modules\admin\controllers;

use Yii;
use DateTime;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Sort;
use yii\data\Pagination;

use app\models\SlypeeUser;
use app\modules\admin\models\PerPageSettings;

class UsersController extends \yii\web\Controller
{
    public $auth;
    public $roles_array = [];

    public function init()
    {
        $this->auth = Yii::$app->authManager;

        parent::init();
    }

    public function actionIndex()
    {
//        if (!Yii::$app->user->can('viewCategory')) {
//            return $this->goHome();
//        }

        $per_page_settings = PerPageSettings::find()->where(['name' => 'users'])->one();

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

        $query = SlypeeUser::find();

        $countQuery = clone $query;

        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => false, 'pageSize' => $page_size]);

        $users = $query->orderBy($sort->orders)->offset($pages->offset)->limit($pages->limit)->all();

        if($users) {
            foreach ($users as $user) {
                $user->roleName = $user->id;
            }
        }

        return $this->render('index', [
            "users" => $users,
            "pages" => $pages,
            "sort" => $sort
        ]);
    }

    public function actionCreate()
    {
        $model = new SlypeeUser;
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
        $model = SlypeeUser::find()->where(['id' => $id])->one();
        $model->scenario = "userUpdate";

        if(!$model) {
            throw new NotFoundHttpException('Category not found' ,404);
        }

        $roles = $this->auth->getAssignments($model->id);

        // one role
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

    public function actionView()
    {

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

}
