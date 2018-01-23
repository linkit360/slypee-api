<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Category Log Filtering
 */
class CategoryLogFilterForm extends Model
{
    public $date_begin;
    public $date_end;
    public $category_id;
    public $user_id;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['date_end'], 'date', 'format' => 'php:m-d-Y'],
            [['category_id', 'user_id'], 'filter', 'filter' => 'intval'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'date_begin' => 'Date from',
            'date_end' => 'Date to',
            'category_id' => 'Category',
            'user_id' => 'User'
        ];
    }

    public function formName()
    {
        return "";
    }
}
