<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "content".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $logo
 * @property string $description
 * @property double $rating
 * @property integer $price
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Category $category
 */
class Content extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'content_type_id', 'currency_type_id', 'name', 'rating', 'price', 'created_at', 'updated_at'], 'required'],
            [['category_id', 'price', 'created_at', 'updated_at', 'active'], 'integer'],
            [['description'], 'string'],
            [['rating'], 'number', 'min' => 0, 'max' => 5],
            [['price'], 'number', 'min' => 0],
            [['name'], 'string', 'max' => 128],
            [['logo'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['content_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentTypes::className(), 'targetAttribute' => ['content_type_id' => 'id']],
            [['currency_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CurrencyTypes::className(), 'targetAttribute' => ['currency_type_id' => 'id']],
            [['price', 'active'], 'filter', 'filter' => 'intval'],
            [['rating'], 'filter', 'filter' => 'floatval']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category',
            'content_type_id' => 'Type',
            'currency_type_id' => 'Currency type',
            'name' => 'Name',
            'logo' => 'Logo',
            'description' => 'Description',
            'rating' => 'Rating',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getContentType()
    {
        return $this->hasOne(ContentTypes::className(), ['id' => 'content_type_id']);
    }

    public function getCurrencyType()
    {
        return $this->hasOne(CurrencyTypes::className(), ['id' => 'currency_type_id']);
    }
}
