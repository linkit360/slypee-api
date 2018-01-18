<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "related_content".
 *
 * @property integer $id
 * @property integer $content_id_a
 * @property integer $content_id_b
 *
 * @property Content $contentIdA
 */
class RelatedContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'related_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_id_a', 'content_id_b'], 'required'],
            [['content_id_a', 'content_id_b'], 'integer'],
            [['content_id_a', 'content_id_b'], 'unique', 'targetAttribute' => ['content_id_a', 'content_id_b'], 'message' => 'The combination of Content Id A and Content Id B has already been taken.'],
            [['content_id_a'], 'exist', 'skipOnError' => true, 'targetClass' => Content::className(), 'targetAttribute' => ['content_id_a' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content_id_a' => 'Content Id A',
            'content_id_b' => 'Content Id B',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentIdA()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id_a']);
    }
}
