<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\imagine\Image;

use app\models\Log;

/**
 * This is the model class for table "customers".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $content
 *
 * @property CustomersContent[] $customersContents
 */
class Customers extends ActiveRecord implements IdentityInterface
{
    const MIN_PASSWORD_LEN = 8;
    private $logType = 'customer';

    public $password;
    public $password_confirm;
    public $old_password;

    public $uploadPath = "uploads/avatars/";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customers';
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
            [['username', 'email', 'created_at', 'updated_at', 'content'], 'required'],
            [['password', 'password_confirm'], 'required', 'on' => 'customerCreate'],
            [['password', 'password_confirm'], 'string', 'min' => self::MIN_PASSWORD_LEN, 'on' => 'customerCreate'],
            [['password', 'password_confirm'], 'validatePasswordOnEdit', 'on' => 'customerUpdate'],
            [['password', 'password_confirm'], 'validatePasswordOnEdit', 'on' => 'customerApiUpdate'],
            [['old_password'], 'validatePasswordOnApiEdit', 'on' => 'customerApiUpdate'],
            ['email', 'email'],
            ['password_confirm', 'validateConfirmedPassword'],
            [['status', 'created_at', 'updated_at', 'content'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['avatar', 'string'],
            ['active', 'filter', 'filter' => function ($value) {
                return Yii::$app->params["connection_type"] == "pgsql" ? (intval($value) ? true:false) : intval($value);
            }],
            //[['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'on' => 'customerApiUpdate'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'content' => 'Content',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomersContents()
    {
        return $this->hasMany(CustomersContent::className(), ['customer_id' => 'id']);
    }

    public function validateConfirmedPassword($attribute, $params)
    {
        if ($this->$attribute != $this->password) {
            $this->addError($attribute, 'Passwords are not equal');
        }
    }

    public function validatePasswordOnEdit($attribute, $params)
    {
        if($this->$attribute) {
            $len = mb_strlen($this->$attribute, "utf-8");
            if($len < self::MIN_PASSWORD_LEN) {
                $this->addError($attribute, "Password should contain at least ".self::MIN_PASSWORD_LEN." characters.");
            }
        }
    }

    public function validatePasswordOnApiEdit($attribute, $params)
    {
        $old_password = $this->$attribute;
        if(!$this->validatePassword($old_password)) {
            $this->addError($attribute, "Current password is wrong");
        }

        # also check new passwords
        if(!$this->password) {
            $this->addError($attribute, "You must enter a new password.");
        }

        //$this->validatePasswordOnEdit("password", null);
        $this->validateConfirmedPassword("password_confirm", null);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'active' => 1]);
    }


    public static function findByPasswordResetToken($token)
    {
        return static::findOne(['password_reset_token' => $token, 'active' => 1]);
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'active' => 1]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'active' => 1]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public  function changeAuthToken()
    {
        $this->generateAuthKey();
        $this->save();
    }

    function setPasswordRecoveryToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString();
        $this->save();

        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params["robot_email"])
            ->setTo($this->email)
            ->setSubject('Slypee: recovery password')
            ->setHtmlBody('<a href="https://portal.linkit360.ru/recovery-password/'. $this->password_reset_token .'">https://portal.linkit360.ru/recovery-password/'.$this->password_reset_token.'</a>')
            ->send();
    }

    function setNewPassword($password)
    {
        $this->password_reset_token = '';
        $this->setPassword($password);
        $this->save();
    }

    public function prepareForApi()
    {
        return [
            "name" => $this->username,
            "email" => $this->email,
            "avatar" => $this->avatar ? Yii::$app->urlManager->createAbsoluteUrl(['/']) . $this->uploadPath . $this->thumbnail:"",
            "token" => $this->auth_key
        ];
    }

    public function setThumbnail($value)
    {
        $this->avatar = $value;
    }

    public function getThumbnail()
    {
        $thumb = (dirname($this->avatar) != "." ? dirname($this->avatar)."/": "") . "s_" . basename($this->avatar);
        return $thumb;
    }

    public function saveAvatar()
    {
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->avatar));
        $new_avatar_name = Yii::$app->security->generateRandomString(8) . '.png';

        $path = preg_replace("/(\d.+)(\d{3})(\d{3})$/", "$1/$2/$3/", sprintf('%09d', $this->id));

        if(!file_exists($this->uploadPath . $path)) {
            mkdir($this->uploadPath . $path, 0777, true);
        }

        file_put_contents($this->uploadPath . $path . $new_avatar_name, $data);
        Image::thumbnail($this->uploadPath . $path . $new_avatar_name, 500, 500)->save($this->uploadPath . $path . "s_". $new_avatar_name, ['jpeg_quality' => 95]);

        $this->avatar = $path . $new_avatar_name;
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

                // emails
                if(isset($changedAttributes["email"])) {
                    $this->emailEmail($changedAttributes["email"]);
                }

                if(isset($changedAttributes["password_hash"])) {
                    $this->emailPassword();
                }
            }
        }
    }

    private function emailPassword()
    {
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params["robot_email"])
            ->setTo($this->email)
            ->setSubject('Slypee: your password was changed')
            ->setHtmlBody('Your password was changed by '.Yii::$app->user->identity->username.'.<br/> Your new password is '.$this->password)
            ->send();
    }

    private function emailEmail($email)
    {
        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params["robot_email"])
            ->setTo($email)
            ->setSubject('Slypee: your email was changed')
            ->setHtmlBody('Your email was changed by '.Yii::$app->user->identity->username.'.<br/> Your new password is '.$this->email)
            ->send();
    }
}
