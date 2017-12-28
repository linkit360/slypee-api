<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * CategoryForm
 */
class CategoryForm extends Model
{
    public $name;
    public $description;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
        ];
    }
}
