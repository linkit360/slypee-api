<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "per_page_settings".
 *
 * @property integer $id
 * @property string $name
 * @property integer $value
 */
class PerPageSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'per_page_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['value'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }

    public function formName()
    {
        return "";
    }
}
