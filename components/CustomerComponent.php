<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\web\IdentityInterface;

/**
 * Class CustomerComponent
 * @package app\components
 * Class for api auth
 * Always use auth token method
 */
class CustomerComponent extends Component{
    /**
     * @var string the class name of the [[identity]] object.
     */
    public $identityClass;

    public function init(){
        parent::init();

        if ($this->identityClass === null) {
            throw new InvalidConfigException('Customer::identityClass must be set.');
        }
    }

    private $_identity = false;

    public function getIdentity()
    {
        if ($this->_identity === false) {
            $this->_identity = null;
            $this->renewAuthStatus();
        }

        return $this->_identity;
    }

    public function setIdentity($identity)
    {
        if ($identity instanceof IdentityInterface) {
            $this->_identity = $identity;
        } elseif ($identity === null) {
            $this->_identity = null;
        } else {
            throw new InvalidValueException('The identity object must implement IdentityInterface.');
        }
    }

    public function getIsGuest()
    {
        return $this->getIdentity() === null;
    }

    public function switchIdentity($identity, $duration = 0)
    {
        $this->setIdentity($identity);
    }

    public function login(IdentityInterface $identity, $duration = 0)
    {
        $this->switchIdentity($identity, $duration);
        return !$this->getIsGuest();
    }

    public function loginByAccessToken($token, $type = null)
    {
        /* @var $class IdentityInterface */
        $class = $this->identityClass;
        $identity = $class::findIdentityByAccessToken($token, $type);
        if ($identity && $this->login($identity)) {
            return $identity;
        }

        return null;
    }

    public function logout()
    {
        $identity = $this->getIdentity();
        if ($identity !== null) {
            $this->switchIdentity(null);
        }

        return $this->getIsGuest();
    }

    protected function renewAuthStatus()
    {
        $headers = Yii::$app->request->headers;
        $token =  isset($headers["x-slypee-auth-token"]) && $headers["x-slypee-auth-token"] ? $headers["x-slypee-auth-token"]: null;
        if ($token === null) {
            $identity = null;
        } else {
            /* @var $class IdentityInterface */
            $class = $this->identityClass;
            $identity = $class::findIdentityByAccessToken($token);
        }

        $this->setIdentity($identity);
    }
}