<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "content_photos".
 *
 * @property int $id
 * @property int $content_id
 * @property int $photo_id
 *
 * @property Content $content
 * @property Photos $photo
 */
class ContentPhotos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content_photos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_id', 'photo_id'], 'required'],
            [['content_id', 'photo_id'], 'integer'],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::className(), 'targetAttribute' => ['content_id' => 'id']],
            [['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Photos::className(), 'targetAttribute' => ['photo_id' => 'id']],
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
            'content_id' => 'Content ID',
            'photo_id' => 'Photo ID',
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
    public function getPhoto()
    {
        return $this->hasOne(Photos::className(), ['id' => 'photo_id']);
    }

    public function add($photo_id, $content_id) {
        $this->load([
            "photo_id" => $photo_id,
            "content_id" => $content_id
        ]);

        $this->save(false);
    }
}
