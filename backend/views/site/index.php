<?php

use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = '';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добро пожаловать!</h1>
        <p class="lead">Данный сервис позволяет вам лично увидеть весь жизненный цикл яблок</p>
        <p><a class="btn btn-lg btn-success" href="<?=Url::to(['/apple'])?>">К яблокам</a></p>
    </div>
</div>
