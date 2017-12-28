<?php

namespace app\models;

use Yii;
use app\models\CategoryLog;
use app\models\CrudTypes;

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
            [['main_menu', 'main_page', 'content', 'priority', 'created_at', 'updated_at', 'active'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['priority'], 'unique'],
            [['main_menu', 'main_page', 'priority', 'active'], 'filter', 'filter' => 'intval']
        ];
    }

//    public function fields()
//    {
//        return [
//            'ResourceID' => 'id'
//        ];
//    }

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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // update action is worked if we change some fields besides active field
        $update = false;

        if($insert) {
            // add new log
            $this->addLog("Add");
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
                        $this->addLog("Activate");
                    } else {
                        $this->addLog("Deactivate");
                    }

                    if(count($changedAttributes) > 1) {
                        $update = true;
                    }

                } else {
                    $update = true;
                }

                if($update) {
                    $this->addLog("Update");
                }


                $this->updated_at = time();
                $this->save();

            }
        }
    }

    private function addLog($type) {

        $crud_type = CrudTypes::find()->where(['name' => $type])->one();

        if(!$crud_type) {
            return;
        }

        $log = new CategoryLog;
        $log->datetime = time();
        $log->crud_type_id = $crud_type->id;
        $log->category_id = $this->id;
        $log->user_id = Yii::$app->user->id;

        $log->save();
    }
}
