<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

use app\models\ContentLog;
use app\models\CrudTypes;

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
    private $oldCategoryId;
    private $oldCategory;

    public $uploadPath = "uploads/content/";

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
            //[['logo'], 'string', 'max' => 255],
            [['logo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['logo'], 'required', 'on' => 'contentCreate'],
            [['video'], 'string', 'max' => 100],
            [['producer'], 'string', 'max' => 50],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['content_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentTypes::className(), 'targetAttribute' => ['content_type_id' => 'id']],
            [['currency_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CurrencyTypes::className(), 'targetAttribute' => ['currency_type_id' => 'id']],
            [['price', 'active', 'category_id', 'content_type_id', 'currency_type_id'], 'filter', 'filter' => 'intval'],
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
            'video' => 'Link to video',
            'producer' => 'Producer'
        ];
    }

    public function formName()
    {
        return "";
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


    public function afterFind()
    {
        $this->oldCategoryId = $this->category_id;
        $this->oldCategory = $this->category;
    }

    // log + category content update!
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // update action is worked if we change some fields besides active field
        $update = false;

        if($insert) {

            // add new log
            $this->addLog("Add");

            if($this->active) {
                $this->category->updateCounters(["content" => 1]);
            }

        } else {

            // check actions
            if(!count($changedAttributes)) {

                return; // nothing to update

            } else {

                $this->updateCategoryContentCount($changedAttributes);

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

    private function updateCategoryContentCount($attributes)
    {
        $isCategoryChange = isset($attributes["category_id"]);
        $isActiveChange = isset($attributes["active"]);

        if($isCategoryChange) {
            if($isActiveChange) {
                if($this->active) {
                    $this->category->updateCounters(["content" => 1]);
                } else {
                    $this->oldCategory->updateCounters(["content" => -1]);
                }
            } else {
                $this->category->updateCounters(["content" => 1]);
                $this->oldCategory->updateCounters(["content" => -1]);
            }
        } else {
            if($isActiveChange) {
                if($this->active) {
                    $this->category->updateCounters(["content" => 1]);
                } else {
                    $this->category->updateCounters(["content" => -1]);
                }
            }
        }

        return true;
    }

    private function addLog($type)
    {

        $crud_type = CrudTypes::find()->where(['name' => $type])->one();

        if(!$crud_type) {
            return;
        }

        $log = new ContentLog();
        $log->datetime = time();
        $log->crud_type_id = $crud_type->id;
        $log->content_id = $this->id;
        $log->user_id = Yii::$app->user->id;

        $log->save();
    }
}
