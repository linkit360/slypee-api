<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

class RolesForm extends Model
{
    public $name;
    public $old_name;
    public $description;
    public $permissions;

    public function rules()
    {
        return [
            [['name'], 'required', 'message' => 'Role name cannot be blank'],
            ['old_name', 'string'],
            ['name', 'validateRoleName'],
            [['description'], 'required', 'message' => 'Role description cannot be blank'],
            [['permissions'], 'required', 'message' => 'Role must has at least one permission']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Role name',
            'description' => 'Role description',
            'permissions' => 'Role permissions'
        ];
    }

    public function formName()
    {
        return "";
    }

    public function validateRoleName($attribute, $params)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($this->$attribute);
        if ($role && $this->$attribute != $this->old_name) {
            $this->addError($attribute, 'Role with name '.ucfirst($this->$attribute).' exists');
        }
    }
}