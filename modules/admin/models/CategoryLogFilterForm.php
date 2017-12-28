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
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['date_end'], 'date', 'format' => 'php:m-d-Y'],
        ];
    }

    public function formName()
    {
        return "";
    }
}
