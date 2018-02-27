<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Log Filtering
 */
class UserLogFilterForm extends Model
{
    public $date_begin;
    public $date_end;
    public $object_type;
    public $types = ["customer" => "Customers", "user" => "Users", "slider" => "Slider", "content" => "Content", "category" => "Category"];
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['date_end'], 'date', 'format' => 'php:m-d-Y'],
            ['object_type', 'string'],
            ['object_type', 'isValidType']
        ];
    }

    public function isValidType($attribute, $params)
    {
        if($this->$attribute) {
            if(!array_key_exists($this->$attribute, $this->types)) {
                $this->addError($attribute, 'Type is not valid');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'date_begin' => 'Date from',
            'date_end' => 'Date to',
            'object_id' => 'Object',
            'user_id' => 'User'
        ];
    }

    public function formName()
    {
        return "";
    }
}
