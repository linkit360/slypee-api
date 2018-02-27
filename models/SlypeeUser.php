<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

use app\models\Log;

class SlypeeUser extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const MIN_PASSWORD_LEN = 5;
    private $logType = 'user';

    public $password;
    public $password_confirm;
    public $role;

    public $is_admin = false; // bool - true if user has Admin role

    private $_roleName;

    public function getRoleName()
    {
        return $this->_roleName;
    }

    public function setRoleName($value)
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getAssignments($value);
        $this->_roleName = implode(array_keys ($roles), " ");
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function formName()
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'role', 'created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['password', 'password_confirm'], 'required', 'on' => 'userCreate'],
            [['password', 'password_confirm'], 'string', 'min' => self::MIN_PASSWORD_LEN, 'on' => 'userCreate'],
            [['password', 'password_confirm'], 'validatePasswordOnEdit', 'on' => 'userUpdate'],
            ['password_confirm', 'validateConfirmedPassword'],
            ['email', 'email'],
            [['email', 'username'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['active', 'filter', 'filter' => function ($value) {
                return Yii::$app->params["connection_type"] == "pgsql" ? (intval($value) ? true:false) : intval($value);
            }],
        ];
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
                $this->addError($attribute, "Password should contain at least 5 characters.");
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'active' => 1]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(Yii::$app->user->isGuest) {
            return;
        }

        // update action is worked if we change some fields besides active field
        $update = false;

        // TODO fix - bug only in this model with attribute - updated_at
        // кстати, возможно это лучший способ обойти игнор атрибута update_at
        unset($changedAttributes["updated_at"]);

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
