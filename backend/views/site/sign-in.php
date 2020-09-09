<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $loginForm \backend\forms\auth\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-sm-offset-3">
            <h1><?= Html::encode($this->title) ?></h1>
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($loginForm, 'email')->textInput() ?>
            <?= $form->field($loginForm, 'password')->passwordInput() ?>
            <?= $form->field($loginForm, 'rememberMe')->checkbox() ?>
            <?php if($loginForm->isUseCaptcha()): ?>
                <?= $form->field($loginForm, 'reCaptcha')
                   ->widget(\himiklab\yii2\recaptcha\ReCaptcha::class, $loginForm->captchaWidgetConfig())
                ?>
            <?php endif;?>
            <div class="form-group">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
