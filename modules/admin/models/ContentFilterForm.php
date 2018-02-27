<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Category Filtering
 */
class ContentFilterForm extends Model
{
    public $name;
    public $created_date_begin;
    public $created_date_end;
    public $updated_date_begin;
    public $updated_date_end;
    public $active;
    public $id;
    public $type;
    public $content_type;
    public $currency_type;
    public $category;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            //[['name', 'date_begin'], 'required'],
            ['active', 'default', 'value'=>'yes'],
            ['type', 'default', 'value'=>'name'],
            [['created_date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['created_date_end'], 'date', 'format' => 'php:m-d-Y'],
            [['updated_date_begin'], 'date', 'format' => 'php:m-d-Y'],
            [['updated_date_end'], 'date', 'format' => 'php:m-d-Y'],
            [['name'], 'string', 'max' => 50],
            [['id', 'category', 'content_type', 'currency_type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Search by:',
            'name' => '',
            'id' => '',
            'currency_type' => 'Currency'
        ];
    }

    public function formName()
    {
        return "";
    }
}
