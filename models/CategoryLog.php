<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category_log".
 *
 * @property integer $id
 * @property integer $datetime
 * @property integer $user_id
 * @property integer $category_id
 * @property integer $crud_type_id
 *
 * @property Category $category
 * @property CrudTypes $crudType
 * @property SlypeeUser $user
 */
class CategoryLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime', 'user_id', 'category_id', 'crud_type_id'], 'required'],
            [['datetime', 'user_id', 'category_id', 'crud_type_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['crud_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CrudTypes::className(), 'targetAttribute' => ['crud_type_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => SlypeeUser::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'user_id' => 'User ID',
            'category_id' => 'Category ID',
            'crud_type_id' => 'Crud Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrudType()
    {
        return $this->hasOne(CrudTypes::className(), ['id' => 'crud_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(SlypeeUser::className(), ['id' => 'user_id']);
    }
}
