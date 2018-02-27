<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Log Filtering
 */
class UsersFilterForm extends Model
{
    public $name;
    public $created_date_begin;
    public $created_date_end;
    public $updated_date_begin;
    public $updated_date_end;
    public $active;
    public $email;
    public $type;
    public $id;
    public $role;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['active', 'default', 'value'=>'yes'],
            ['type', 'default', 'value'=>'name'],
            [['created_date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['created_date_end'], 'date', 'format' => 'php:m-d-Y'],
            [['updated_date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['updated_date_end'], 'date', 'format' => 'php:m-d-Y'],
            [['name', 'email', 'role'], 'string', 'max' => 50],
            [['id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => 'Search by:',
            'name' => '',
            'email' => '',
            'id' => ''
        ];
    }

    public function formName()
    {
        return "";
    }
}
