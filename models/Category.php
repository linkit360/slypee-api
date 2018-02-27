<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;

//use app\models\CategoryLog;
use app\models\Log;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $main_menu
 * @property integer $main_page
 * @property integer $content
 * @property integer $priority
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $active
 */
class Category extends \yii\db\ActiveRecord
{
    private $logType = 'category';

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'content', 'priority', 'created_at', 'updated_at'], 'required'],
            [['description'], 'string'],
            [['content', 'priority', 'created_at', 'updated_at', 'active'], 'integer'], /* 'main_menu', 'main_page' */
            [['name'], 'string', 'max' => 50],
            [['priority', 'name'], 'unique'],
            [['priority', 'active'], 'filter', 'filter' => 'intval'],
            [['main_menu', 'main_page'], 'filter', 'filter' => function ($value) {
                return Yii::$app->params["connection_type"] == "pgsql" ? (intval($value) ? true:false) : intval($value);
            }],
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
            'description' => 'Description',
            'main_menu' => 'Main Menu',
            'main_page' => 'Main Page',
            'content' => 'Content',
            'priority' => 'Priority',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'active' => 'Active',
        ];
    }

    public function formName()
    {
        return "";
    }

    public function fields()
    {
        return [
            // field name is the same as the attribute name
            'id', 'name', 'description', 'content', 'slug'
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(Yii::$app->user->isGuest) {
            return;
        }

        // update action is worked if we change some fields besides active field
        $update = false;

        if($insert) {
            // add new log
            (new Log)->addLog($this->id, $this->logType, "Add");
        } else {
            // check actions
            if(!count($changedAttributes)) {
                return; // nothing to update
            } else {

                if(isset($changedAttributes["updated_at"])) {
                    return; // after update updated time
                }

                if(isset($changedAttributes["active"])) {
                    // activate or deactivate
                    if($this->active) {
                        (new Log)->addLog($this->id, $this->logType, "Activate");
                    } else {
                        (new Log)->addLog($this->id, $this->logType, "Deactivate");
                    }

                    if(count($changedAttributes) > 1) {
                        $update = true;
                    }

                } else {
                    $update = true;
                }

                if($update) {
                    (new Log)->addLog($this->id, $this->logType, "Update");
                }

                $this->updated_at = time();
                $this->save();
            }
        }
    }
}
