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
class RecoveryForm extends Model
{
    public $email;

    private $_customer = false;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'validateEmailExist']
        ];
    }

    public function validateEmailExist($attribute, $params)
    {
        if(!$this->customer) {
            $this->addError($attribute, 'Incorrect email.');
        }
    }

    public function formName()
    {
        return "";
    }

    public function recovery()
    {
        if ($this->validate()) {

            // send recovery email
            $this->customer->setPasswordRecoveryToken();

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
