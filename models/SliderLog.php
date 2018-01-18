<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "slider_log".
 *
 * @property integer $id
 * @property integer $datetime
 * @property integer $user_id
 * @property integer $slide_id
 * @property integer $crud_type_id
 *
 * @property CrudTypes $crudType
 * @property Slider $slide
 * @property User $user
 */
class SliderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'slider_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime', 'user_id', 'slide_id', 'crud_type_id'], 'required'],
            [['datetime', 'user_id', 'slide_id', 'crud_type_id'], 'integer'],
            [['crud_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CrudTypes::className(), 'targetAttribute' => ['crud_type_id' => 'id']],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => Slider::className(), 'targetAttribute' => ['slide_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'slide_id' => 'Slide ID',
            'crud_type_id' => 'Crud Type ID',
        ];
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
    public function getSlide()
    {
        return $this->hasOne(Slider::className(), ['id' => 'slide_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
