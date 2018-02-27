<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Category Filtering
 */
class SliderFilterForm extends Model
{
    public $name;
    public $active;
    public $id;
    public $type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            //[['name', 'date_begin'], 'required'],
            ['active', 'default', 'value'=>'yes'],
            ['type', 'default', 'value'=>'name'],
            [['name'], 'string', 'max' => 50],
            [['id'], 'integer'],
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
            'id' => ''
        ];
    }

    public function formName()
    {
        return "";
    }
}
