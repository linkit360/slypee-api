<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "crud_types".
 *
 * @property integer $id
 * @property string $name
 *
 * @property CategoryLog[] $categoryLogs
 */
class CrudTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crud_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryLogs()
    {
        return $this->hasMany(CategoryLog::className(), ['crud_type_id' => 'id']);
    }
}
