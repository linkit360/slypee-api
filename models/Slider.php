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
            [['title', 'subtitle', 'link', 'image', 'priority', 'created_at', 'updated_at'], 'required'],
            [['description'], 'string'],
            [['priority', 'created_at', 'updated_at'], 'integer'],
            [['title', 'subtitle'], 'string', 'max' => 50],
            [['link'], 'string', 'max' => 128],
            //[['image'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
            [['priority'], 'unique'],
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
        ];
    }
}