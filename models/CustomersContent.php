<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customers_content".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $content_id
 * @property integer $type_id
 * @property integer $currency_id
 * @property integer $price
 * @property integer $date
 * @property string $token
 * @property integer $status
 *
 * @property Content $content
 * @property CurrencyTypes $currency
 * @property Customers $customer
 * @property ContentTypes $type
 */
class CustomersContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customers_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'content_id', 'type_id', 'currency_id', 'price', 'date', 'status'], 'integer'],
            [['content_id', 'type_id', 'currency_id', 'price', 'date'], 'required'],
            [['token'], 'string', 'max' => 255],
            [['token'], 'unique'],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::className(), 'targetAttribute' => ['content_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => CurrencyTypes::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customers::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentTypes::className(), 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'content_id' => 'Content ID',
            'type_id' => 'Type ID',
            'currency_id' => 'Currency ID',
            'price' => 'Price',
            'date' => 'Date',
            'token' => 'Token',
            'status' => 'Status',
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
    public function getCurrency()
    {
        return $this->hasOne(CurrencyTypes::className(), ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ContentTypes::className(), ['id' => 'type_id']);
    }

    public function formName()
    {
        return "";
    }
}
