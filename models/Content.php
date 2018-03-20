<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;
use yii\imagine\Image;

use app\models\RelatedContents;

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
    public $photos_ids;

    public $uploadPath = "uploads/content/";
    private $logType = 'content';

    private $_related = false;
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
            [['category_id', 'price', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['rating'], 'number', 'min' => 0, 'max' => 5],
            [['price'], 'number', 'min' => 0],
            [['name'], 'string', 'max' => 50],
            //[['logo'], 'string', 'max' => 255],
            [['logo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['logo'], 'required', 'on' => 'contentCreate'],
            [['video'], 'string', 'max' => 100],
            [['producer'], 'string', 'max' => 50],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['content_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentTypes::className(), 'targetAttribute' => ['content_type_id' => 'id']],
            [['currency_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CurrencyTypes::className(), 'targetAttribute' => ['currency_type_id' => 'id']],
            [['price', 'category_id', 'content_type_id', 'currency_type_id'], 'filter', 'filter' => 'intval'],
            [['rating'], 'filter', 'filter' => 'floatval'],
            ['photos_ids', 'each', 'rule' => ['integer']],
            ['active', 'filter', 'filter' => function ($value) {
                //die(Yii::$app->params["connection_type"]." | ".$value);
                return Yii::$app->params["connection_type"] == "pgsql" ? (intval($value) ? true:false) : intval($value);
            }],
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

    public function saveLogo()
    {
        $new_logo_name = Yii::$app->security->generateRandomString(8) . '.' . $this->logo->extension;
        $this->logo->name = $new_logo_name;

        $path = preg_replace("/(\d.+)(\d{3})(\d{3})$/", "$1/$2/$3/", sprintf('%09d', $this->id));

        if(!file_exists($this->uploadPath . $path)) {
            mkdir($this->uploadPath . $path, 0777, true);
        }

        $this->logo->saveAs($this->uploadPath . $path . $new_logo_name);
        Image::thumbnail($this->uploadPath . $path . $new_logo_name, 250, 250)->save($this->uploadPath . $path . "s_". $new_logo_name, ['jpeg_quality' => 95]);

        $this->logo = $path . $new_logo_name;
        $this->save();
    }

    public function getRelated()
    {
        if ($this->_related === false) {
            $related_data = RelatedContent::find()->orWhere(["content_id_a" => $this->id])->orWhere(["content_id_b" => $this->id])->all();
            $related_content = [];

            foreach ($related_data as $r) {

                if ($r->contentIdA->id == $this->id) {
                    $related_content[] = [
                        "id" => $r->id,
                        "logo" => "/" . $r->contentIdB->uploadPath . $r->contentIdB->logo,
                        "content" => $r->content_id_b,
                        "name" => $r->contentIdB->name
                    ];
                }

                if ($r->contentIdB->id == $this->id) {
                    $related_content[] = [
                        "id" => $r->id,
                        "logo" => "/" . $r->contentIdA->uploadPath . $r->contentIdA->logo,
                        "content" => $r->content_id_a,
                        "name" => $r->contentIdA->name
                    ];
                }
            }

            $this->_related = $related_content;
        }

        return $this->_related;
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

    public function getContentPhotos()
    {
        return $this->hasMany(ContentPhotos::className(), ['content_id' => 'id'])->orderBy(['priority' => SORT_ASC]);
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

        if(Yii::$app->user->isGuest) {
            return;
        }

        // update action is worked if we change some fields besides active field
        $update = false;

        if($insert) {

            // add new log
            (new Log)->addLog($this->id, $this->logType, "Add");

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

    public function prepareForListApi() {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "price" => $this->price,
            "rating" => $this->rating,
            "type" => $this->contentType->name,
            "currency" => $this->currencyType->name,
            "logo" => Yii::$app->urlManager->createAbsoluteUrl(['/']) . $this->uploadPath . $this->thumbnail,
            "categoryId" => $this->category_id,
            "producer" => $this->producer
        ];
    }

    public function prepareForApi() {
        $screenshots = [];
        $preparedRelated = [];
        $absoluteUrl = Yii::$app->urlManager->createAbsoluteUrl(['/']);

        if($this->contentPhotos) {
            foreach($this->contentPhotos as $photo) {
                $screenshots[] = [
                    "src" => $absoluteUrl.$photo->photo->image,
                    "thumbnail" => $absoluteUrl.$photo->photo->thumbnail
                ];
            }
        }

        $related = RelatedContent::find()->orWhere(["content_id_a" => $this->id])->orWhere(["content_id_b" => $this->id])->all();

        if($related) {
            foreach ($related as $r) {
                // check active content
                if($r->contentIdA->active && $r->contentIdB->active && $r->contentIdA->category->active && $r->contentIdB->category->active) {

                    $preparedRelated[] = $r->prepareForApi($this->id);

                }
            }
        }

        $owned = 0;
        if(!Yii::$app->customer->isGuest) {
            $customer_content = CustomersContent::find()->andWhere(["status" => 1, "customer_id" => Yii::$app->customer->identity->id, "content_id" => $this->id])->one();
            $owned = $customer_content ? 1:0;
        }

        return [
            "id" => $this->id,
            "isOwned" => $owned,
            "name" => $this->name,
            "description" => $this->description,
            "price" => $this->price,
            "rating" => $this->rating,
            "type" => $this->contentType->name,
            "currency" => $this->currencyType->name,
            "logo" => $absoluteUrl.$this->uploadPath.$this->logo,
            "thumbnailLogo" => Yii::$app->urlManager->createAbsoluteUrl(['/']) . $this->uploadPath . $this->thumbnail,
            "categoryId" => $this->category_id,
            "screenshots" => $screenshots,
            "video" => $this->video,
            "producer" => $this->producer,
            "related" => $preparedRelated,
            "relatedLength" => count($preparedRelated)
        ];
    }

    public function savePhotosIds() {
        if($this->photos_ids) {
            foreach ($this->photos_ids as $photo_id) {
                (new ContentPhotos)->add($photo_id, $this->id);
            }
        }
    }

    public function setThumbnail($value)
    {
        $this->logo = $value;
    }

    public function getThumbnail()
    {
        $thumb = (dirname($this->logo) != "." ? dirname($this->logo)."/": "") . "s_" . basename($this->logo);
        return $thumb;
    }
}
