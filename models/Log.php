<?php

namespace app\models;

use Yii;

use app\models\CrudTypes;
/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property integer $datetime
 * @property integer $user_id
 * @property integer $object_id
 * @property integer $crud_type_id
 * @property string $object_type
 *
 * @property CrudTypes $crudType
 * @property User $user
 */
class Log extends \yii\db\ActiveRecord
{
    private $_objectName = false;
    public $user_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    public function formName()
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime', 'user_id', 'object_id', 'crud_type_id'], 'required'],
            [['datetime', 'user_id', 'object_id', 'crud_type_id'], 'integer'],
            [['object_type'], 'string'],
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
            'object_id' => 'Object ID',
            'crud_type_id' => 'Crud Type ID',
            'object_type' => 'Object Type',
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
    public function getUser()
    {
        return $this->hasOne(SlypeeUser::className(), ['id' => 'user_id'])->from(SlypeeUser::tableName() . ' su');
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'object_id']);
    }

    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'object_id']);
    }

    public function getSlider()
    {
        return $this->hasOne(Slider::className(), ['id' => 'object_id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'object_id']);
    }

    public function getLoggedUser()
    {
        return $this->hasOne(SlypeeUser::className(), ['id' => 'object_id']);
    }

    public function getObjectName()
    {
        if ($this->_objectName === false) {
            switch ($this->object_type) {
                case "content":
                    $this->_objectName = $this->content->name;
                    break;
                case "category":
                    $this->_objectName = $this->category->name;
                    break;
                case "user":
                    $this->_objectName = $this->loggedUser->username;
                    break;
                case "customer":
                    $this->_objectName = $this->customer->username;
                    break;
                case "slider":
                    $this->_objectName = $this->slider->title;
                    break;
            }
        }

        return $this->_objectName;
    }

    public function addLog($object_id, $object_type, $type) {

        if(!Yii::$app->user->id) {
            return;
        }

        $crud_type = CrudTypes::find()->where(['name' => $type])->one();

        if(!$crud_type) {
            return;
        }

        $this->load([
            "datetime" => time(),
            "crud_type_id" => $crud_type->id,
            "user_id" => Yii::$app->user->id,
            "object_type" => $object_type,
            "object_id" => $object_id
        ]);

        $this->save(false);
    }
}
