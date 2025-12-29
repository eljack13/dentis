<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = null; // ✅ IMPORTANTÍSIMO: null, no false

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'string', 'max' => 120],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Correo o contraseña incorrectos.');
        }
    }

    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        return Yii::$app->user->login(
            $this->getUser(),
            $this->rememberMe ? 3600 * 24 * 30 : 0
        );
    }

    public function getUser()
    {
        if ($this->_user !== null) {
            return $this->_user;
        }

        $this->_user = Usuario::findOne([
            'email' => $this->email,
            'status' => 1,
        ]);

        return $this->_user;
    }
}
