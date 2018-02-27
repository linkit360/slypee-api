<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "slider".
 *
 * @property integer $id
 * @property string $title
 * @property string $subtitle
 * @property string $description
 * @property string $link
 * @property string $image
 * @property integer $priority
 * @property integer $created_at
 * @property integer $updated_at
 */
class Slider extends \yii\db\ActiveRecord
{
    public $uploadPath = "uploads/slider/";
    public $logType = "slider";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'slider';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'subtitle', 'link', 'priority', 'created_at', 'updated_at'], 'required'],
            [['description'], 'string'],
            [['priority', 'created_at', 'updated_at'], 'integer'],
            [['title', 'subtitle'], 'string', 'max' => 50],
            [['link'], 'string', 'max' => 128],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['image'], 'required', 'on' => 'sliderCreate'],
            [['priority'], 'unique'],
            [['priority'], 'filter', 'filter' => 'intval'],
            ['active', 'filter', 'filter' => function ($value) {
                return Yii::$app->params["connection_type"] == "pgsql" ? (intval($value) ? true:false) : intval($value);
            }],
        ];
    }

    public function formName()
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'subtitle' => 'Subtitle',
            'description' => 'Description',
            'link' => 'Link',
            'image' => 'Image',
            'priority' => 'Priority',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'active' => 'Active',
        ];
    }

    public function fields()
    {
        return [
            // field name is the same as the attribute name
            'title', 'subtitle', 'description', 'link',
            'image' => function ($model) {
                return Yii::$app->urlManager->createAbsoluteUrl(['/']).$model->uploadPath.$model->image;
            },
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

    public function saveImage()
    {
        $new_image_name = Yii::$app->security->generateRandomString(8) . '.' . $this->image->extension;
        $this->image->name = $new_image_name;

        $path = preg_replace("/(\d.+)(\d{3})(\d{3})$/", "$1/$2/$3/", sprintf('%09d', $this->id));

        if(!file_exists($this->uploadPath . $path)) {
            mkdir($this->uploadPath . $path, 0777, true);
        }

        $this->image->saveAs($this->uploadPath . $path . $new_image_name);

        $this->image = $path . $new_image_name;
        $this->save();
    }
}
