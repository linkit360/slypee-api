<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Model;
use app\models\Customers;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RecoveryConfirmForm extends Model
{
    const MIN_PASSWORD_LEN = 8;

    public $password;
    public $password_confirm;
    public $token;

    private $_customer = false;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password', 'password_confirm', 'token'], 'required'],
            ['token', 'validateTokenExist'],
            ['password_confirm', 'validateConfirmedPassword'],
            [['password', 'password_confirm'], 'validatePasswordOnEdit'],
        ];
    }

    public function validateTokenExist($attribute, $params)
    {
        if(!$this->customer) {
            $this->addError($attribute, 'Incorrect token.');
        }
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

    public function formName()
    {
        return "";
    }

    public function recovery()
    {
        if ($this->validate()) {

            $this->customer->setNewPassword($this->password);

        } else {

            return false;
        }

    }

    public function getCustomer()
    {
        if ($this->_customer === false) {
            $this->_customer = Customers::findByPasswordResetToken($this->token);
        }

        return $this->_customer;
    }
}
