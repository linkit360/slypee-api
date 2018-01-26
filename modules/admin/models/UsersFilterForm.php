<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Log Filtering
 */
class UsersFilterForm extends Model
{
    public $created_date_begin;
    public $created_date_end;
    public $updated_date_begin;
    public $updated_date_end;
    public $active;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['active', 'default', 'value'=>'yes'],
            [['created_date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['created_date_end'], 'date', 'format' => 'php:m-d-Y'],
            [['updated_date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['updated_date_end'], 'date', 'format' => 'php:m-d-Y'],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function formName()
    {
        return "";
    }
}
