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
            [['content_id_a', 'content_id_b'], 'required'],
            [['content_id_a', 'content_id_b'], 'integer'],
            [['content_id_a'], 'checkEqual'],
            [['content_id_a'], 'checkInverse'],
            [['content_id_a', 'content_id_b'], 'unique', 'targetAttribute' => ['content_id_a', 'content_id_b'], 'message' => 'The combination of Content Id A and Content Id B has already been taken.'],
            [['content_id_a'], 'exist', 'skipOnError' => true, 'targetClass' => Content::className(), 'targetAttribute' => ['content_id_a' => 'id']],
            [['content_id_b'], 'exist', 'skipOnError' => true, 'targetClass' => Content::className(), 'targetAttribute' => ['content_id_b' => 'id']],
            [['content_id_a', 'content_id_b'], 'filter', 'filter' => 'intval'],
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

    public function checkEqual($attribute, $params)
    {
        if($this->$attribute) {
            if($this->$attribute == $this->content_id_b) {
                $this->addError($attribute, "Error! items are equal");
            }
        }
    }

    public function checkInverse($attribute, $params)
    {
        if($this->$attribute) {
            $inversed = $this::findOne(["content_id_b" => $this->$attribute, "content_id_a" => $this->content_id_b]);
            if($inversed) {
                $this->addError($attribute, "Error. Save pair of contents already in use");
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentIdA()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id_a']);
    }

    public function getContentIdB()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id_b']);
    }

    public function prepareForApi($id)
    {
        if($this->contentIdA->id == $id) {
            return $this->contentIdB->prepareForListApi();
        }

        if($this->contentIdB->id == $id) {
            return $this->contentIdA->prepareForListApi();
        }
    }
}
