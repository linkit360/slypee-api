<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\modules\admin\models\RolesForm;


class RolesController extends \yii\web\Controller
{
    public $auth;
    private $permissions_array = [];

    public function init()
    {
        $this->auth = Yii::$app->authManager;

        parent::init();
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->identity->is_admin) {
            return $this->goHome();
        }

        $roles = $this->auth->getRoles();

        return $this->render('index', [
            "roles" => $roles
        ]);
    }

    public function actionCreate() {

        if (!Yii::$app->user->identity->is_admin) {
            return $this->goHome();
        }

        $this->getPermissionsArray();

        $model = new RolesForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "Role was successfully added"
            ];

            if ($model->validate()) {

                // все данные корректны
                //$rule = $auth->createPermission('viewCategoryLog');
                //$rule->description = 'View a category log';
                $role = $this->auth->createRole($model->name);
                $role->description = $model->description;
                $this->auth->add($role);

                foreach ($model->permissions as $permission) {
                    $rule = $this->auth->getPermission($permission);
                    $this->auth->addChild($role, $rule);
                }

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

            return $this->render('create', [
                "role" => [],
                "permissions" => $this->permissions_array,
                "model" => $model,
                "btn" => "Create role",
            ]);
        }
    }

    public function actionUpdate($name='')
    {
        if (!Yii::$app->user->identity->is_admin) {
            return $this->goHome();
        }

        if(!$name) {
            throw new NotFoundHttpException('Role not found' ,404);
        }

        if(mb_strtolower($name) == "admin") {
            throw new NotFoundHttpException('You couldn\'t edit admin role' ,404);
        }

        $role = $this->auth->getRole($name);

        if(!$role) {
            throw new NotFoundHttpException('Role not found' ,404);
        }

        $this->getPermissionsArray();

        $roles_permissions = $this->auth->getPermissionsByRole($name);
        $roles_permissions_array = [];

        foreach ($roles_permissions as $permission) {
            $roles_permissions_array[] = $permission->name;
        }

        $model = new RolesForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            $data = [
                "message" => "Role was successfully updated"
            ];

            // save for validation
            $model->old_name = $name;

            if ($model->validate()) {

                $changes = $this->auth->createRole($model->name);
                $changes->description = $model->description;

                $this->auth->update($name, $changes);

                // old permissions
                // $roles_permissions_array

                // new permissions
                // $model->permissions


                $permisions_to_add = array_diff($model->permissions, $roles_permissions_array);
                $permisions_to_delete = array_diff($roles_permissions_array, $model->permissions);

                if($permisions_to_add) {
                    foreach ($permisions_to_add as $permission) {
                        $rule = $this->auth->getPermission($permission);
                        $this->auth->addChild($role, $rule);
                    }
                }

                if($permisions_to_delete) {
                    foreach ($permisions_to_delete as $permission) {
                        $rule = $this->auth->getPermission($permission);
                        $this->auth->removeChild($role, $rule);
                    }
                }

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


            $model->load([
                "name" => $role->name,
                "description" => $role->description,
                "permissions" => $roles_permissions_array
            ]);

            return $this->render('create', [
                "role" => $role,
                "permissions" => $this->permissions_array,
                "model" => $model,
                "btn" => "Save changes",
            ]);
        }
    }

    private function getPermissionsArray()
    {
        $permissions = $this->auth->getPermissions();
        $permissions_array = [];

        foreach ($permissions as $permission) {
            $permissions_array[$permission->name] = $permission->description;
        }

        ksort($permissions_array);

        $this->permissions_array = $permissions_array;
    }

}
