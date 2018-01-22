<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "content_log".
 *
 * @property integer $id
 * @property integer $datetime
 * @property integer $user_id
 * @property integer $content_id
 * @property integer $crud_type_id
 *
 * @property Content $content
 * @property CrudTypes $crudType
 * @property SlypeeUser $user
 */
class ContentLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime', 'user_id', 'content_id', 'crud_type_id'], 'required'],
            [['datetime', 'user_id', 'content_id', 'crud_type_id'], 'integer'],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::className(), 'targetAttribute' => ['content_id' => 'id']],
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
            'content_id' => 'Content ID',
            'crud_type_id' => 'Crud Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
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
