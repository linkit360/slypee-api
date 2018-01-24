<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Log Filtering
 */
class LogFilterForm extends Model
{
    public $date_begin;
    public $date_end;
    public $object_id;
    public $user_id;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['date_end'], 'date', 'format' => 'php:m-d-Y'],
            [['object_id', 'user_id'], 'filter', 'filter' => 'intval'],
        ];
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
