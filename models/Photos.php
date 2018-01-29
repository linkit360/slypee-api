<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * This is the model class for table "photos".
 *
 * @property int $id
 * @property string $image
 * @property int $created_at
 *
 * @property ContentPhotos[] $contentPhotos
 */
class Photos extends \yii\db\ActiveRecord
{
    public $imageFiles;
    public $uploadPath = "uploads/content/photos/";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'photos';
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
            ['created_at', 'required'],
            [['created_at'], 'integer'],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 5],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Image',
            'created_at' => 'Created At',
        ];
    }

    public function upload()
    {
        $this->created_at = time();
        $ids = [];

        if ($this->validate()) {

            foreach ($this->imageFiles as $file) {
                $model = new Photos();

                $name = Yii::$app->security->generateRandomString(8) . '.' . $file->extension;
                $model->image = $this->uploadPath . $name;
                $model->created_at = time();
                $model->save();
                $file->saveAs($model->image);
                $ids[] = $model->id;

                Image::thumbnail($this->uploadPath . $name, null, 300)->save($this->uploadPath . "s_". $name, ['jpeg_quality' => 95]);
            }

            return $ids;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentPhotos()
    {
        return $this->hasMany(ContentPhotos::className(), ['photo_id' => 'id']);
    }

    public function setThumbnail($value)
    {
        $this->image = $value;
    }

    public function getThumbnail()
    {
        $thumb = dirname($this->image). "/s_" .basename($this->image);
        return $thumb;
    }
}
