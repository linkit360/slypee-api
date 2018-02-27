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
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;
    public $active = false;

    private $_customer = false;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            ['email', 'email'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['active', 'validateActive'],
        ];
    }

    public function formName()
    {
        return "";
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getCustomer();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function validateActive($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getCustomer();

            if (!$user || !$user->active) {
                $this->addError($attribute, 'Your user is blocked.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {

            return Yii::$app->customer->login($this->getCustomer(), $this->rememberMe ? 3600*24*30 : 0);

        } else {

            return false;
        }

    }

    public function getCustomer()
    {
        if ($this->_customer === false) {
            $this->_customer = Customers::findByEmail($this->email);
        }

        return $this->_customer;
    }
}
