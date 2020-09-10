<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\fruit\Apple;
use softcommerce\knob\Knob;
use yii\widgets\Pjax;
use backend\models\AppleSearch;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var Apple[] $apples */
/** @var AppleSearch $searchModel */

$this->title = 'Яблоки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <?= Html::a(
                'Сгенерировать яблоки (до 100)',
                Url::to(['/apple/generate']),
                [
                    'class' => 'btn btn-success',
                    'data-method' => 'post',
                    'data-confirm' => 'Вы уверены?',
                ]
            ) ?>
        </div>
    </div>
</div>
<div class="row">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'enableClientValidation' => false,
    ])?>
        <div class="col-sm-4">
            <?=$form->field($searchModel, 'status')->widget(Select2::class, [
                'data' => Apple::getStatusLabels(),
                'theme' => Select2::THEME_DEFAULT,
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'placeholder' => '---'
                ]
            ])?>
        </div>
        <div class="col-sm-4">
            <?=$form->field($searchModel, 'color')->widget(Select2::class, [
                'data' => Apple::getColorLabels(),
                'theme' => Select2::THEME_DEFAULT,
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'placeholder' => '---'
                ]
            ])?>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <?= Html::submitButton('Найти', ['class' => 'btn btn-primary find-button']) ?>
                <?= Html::a('Сбросить', Url::to(['/apple']), ['class' => 'btn btn-default find-button']) ?>
            </div>
        </div>
    <?php $form::end()?>
</div>
<div class="row">
    <?php Pjax::begin([
        'id' => 'pjax-apple'
    ]);?>
    <?php if ($apples):?>
        <?php foreach ($apples as $key => $apple):?>
            <?php if ($key % 4 == 0):?>
                <div class="row">
            <?php endif;?>
            <div class="col-sm-3 text-center fa-border">
                <?=Knob::widget([
                    'value' => round($apple->size, 2) * 100,
                    'knobOptions' => [
                        'readOnly' => true,
                        'thickness' => '.1',
                        'dynamicDraw' => true,
                        'fgColor' => $apple->getColorHex(),
                        'width' => '50px',
                        'height' => '50px',
                        'displayInput' => false
                    ],
                ]);?>
                <table class="table">
                    <tr class="row">
                        <th>Номер:</th>
                        <td class="text-right">#<?=$apple->id?></td>
                    </tr>
                    <tr class="row">
                        <th>Остаток:</th>
                        <td class="text-right"><?=round($apple->size, 2) * 100?> %</td>
                    </tr>
                    <tr class="row">
                        <th>Цвет:</th>
                        <td class="text-right"><?=$apple->getColorLabel()?></td>
                    </tr>
                    <tr class="row">
                        <th>Состояние:</th>
                        <td class="text-right"><?=$apple->getStatusLabel()?></td>
                    </tr>
                    <?php if ($apple->isDropped()):?>
                        <tr class="row">
                            <th>Сгниет:</th>
                            <td class="text-right">
                                <?=date(
                                    'd.m.Y H:i',
                                    strtotime($apple->dropped_at . ' +' .  + Apple::ROTTED_AWAY_AFTER . 'sec'))?>
                            </td>
                        </tr>
                    <?php endif;?>
                    <?php if ($apple->canDrop()):?>
                        <tr class="row">
                            <th></th>
                            <td class="text-right">
                                <a
                                    href="#"
                                    class="btn btn-primary js__ajax-action"
                                    data-url="<?=Url::to(['/ajax/apple/drop', 'id' => $apple->id])?>"
                                    data-pjax-selector="#pjax-apple"
                                    data-confirm="Вы уверены?"
                                >
                                    Сорвать
                                </a>
                            </td>
                        </tr>
                    <?php endif;?>
                    <?php if ($apple->canEat()):?>
                        <tr class="row">
                            <th></th>
                            <td class="text-right">
                                <a
                                    href="#"
                                    class="btn btn-success js__eat"
                                    data-url="<?=Url::to(['/ajax/apple/get-eat-modal', 'id' => $apple->id])?>"
                                    data-target="#modal"
                                >
                                    Съесть
                                </a>
                            </td>
                        </tr>
                    <?php endif;?>
                    <?php if ($apple->canTrash()):?>
                        <tr class="row">
                            <th></th>
                            <td class="text-right">
                                <a
                                    href="#"
                                    class="btn btn-danger js__ajax-action"
                                    data-url="<?=Url::to(['/ajax/apple/trash', 'id' => $apple->id])?>"
                                    data-pjax-selector="#pjax-apple"
                                    data-confirm="Вы уверены?"
                                >
                                    Выкинуть
                                </a>
                            </td>
                        </tr>
                    <?php endif;?>
                </table>
            </div>
            <?php if ($key % 4 == 3):?>
                </div>
            <?php endif;?>
        <?php endforeach;?>
        <?php if ($key % 4 != 3):?>
            </div>
        <?php endif;?>
    <?php else:?>
        <div class="col-sm-12">
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-info"></i> У вас еще нет ни одного яблока</h4>
                Сгенерируйте свои первые яблоки, нажав кнопку выше
            </div>
        </div>
    <?php endif;?>
    <?php Pjax::end()?>
</div>
