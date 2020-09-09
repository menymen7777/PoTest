<?php

namespace backend\forms\auth;

use common\models\User;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use Yii;
use yii\base\Model;

/**
 * Форма авторизации
 * Class LoginForm
 * @package backend\forms\auth
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;
    public $reCaptcha;

    private $_user;

    //настройки для каптчи
    const LIMIT_BAD_TRY_IP = 3;
    const LIMIT_BAD_TRY_EMAIL = 3;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'string', 'max' => 255],
            [['email', 'password'], 'filter' , 'filter' => 'trim'],
            [['email', 'password'], 'filter' , 'filter' => 'strip_tags'],
            [['email'], 'filter' , 'filter' => 'strtolower'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
            [
                ['reCaptcha'],
                'required',
                'when' => function () {
                    return $this->isUseCaptcha();
                },
            ],
            [
                ['reCaptcha'],
                ReCaptchaValidator::class,
                'secret' => Yii::$app->params['captcha']['secretKey'],
                'when' => function (Model $model) {
                    return !$model->errors && !Yii::$app->request->isAjax && $this->isUseCaptcha();
                },
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня?',
            'reCaptcha' => 'ReCaptcha',
        ];
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
        if ($this->isUseCaptcha() && isset($this->firstErrors['reCaptcha'])) {
            $this->addError(
                $attribute,
                '"' . $this->getAttributeLabel('reCaptcha') . '" ' . $this->firstErrors['reCaptcha']
            );

            return;
        }

        $user = $this->getUser();
        $keyIp = $this->cacheKeyBadTryIp();
        $keyEmail = $this->cacheKeyBadTryEmail();
        if (!$user || !$user->isActive() || !$user->validatePassword($this->password)) {
            $this->addError('login', 'Неправильный email или пароль');

            /*счетчик неудачных попыток входа по Ip*/
            $bad_try_input_ip = \Yii::$app->cache->get($keyIp);
            $bad_try_input_email = \Yii::$app->cache->get($keyEmail);

            $bad_try_input_ip = $bad_try_input_ip ? ++$bad_try_input_ip : 1;
            $bad_try_input_email = $bad_try_input_email ? ++$bad_try_input_email : 1;

            Yii::$app->cache->set($keyIp, $bad_try_input_ip);
            Yii::$app->cache->set($keyEmail, $bad_try_input_email);
        } else {
            Yii::$app->cache->delete($keyEmail);
            Yii::$app->cache->delete($keyIp);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if ($this->hasErrors()) {
            $this->password = null;
        }

        parent::afterValidate();
    }

    /**
     * @return bool
     */
    public function isUseCaptcha()
    {
        /**
         * Проверяем количество попыток ввода логин/пароля
         * после трех проверяем на капчу
         */
        $bad_try_input_ip = (int)Yii::$app->cache->get($this->cacheKeyBadTryIp());
        $bad_try_input_email = (int)Yii::$app->cache->get($this->cacheKeyBadTryEmail());

        return ($bad_try_input_ip >= self::LIMIT_BAD_TRY_IP) || ($bad_try_input_email >= self::LIMIT_BAD_TRY_EMAIL);
    }

    /**
     * @return array
     */
    public function captchaWidgetConfig()
    {
        return [
            'siteKey' => Yii::$app->params['captcha']['siteKey'],
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    /**
     * @return string
     */
    protected function cacheKeyBadTryIp()
    {
        return 'bad_try_input_ip_' . Yii::$app->request->getUserIP();
    }

    /**
     * @return string
     */
    protected function cacheKeyBadTryEmail()
    {
        return 'bad_try_input_email_' . $this->email;
    }
}
