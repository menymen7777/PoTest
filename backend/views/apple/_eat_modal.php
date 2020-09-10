<?php

use yii\widgets\ActiveForm;
use backend\forms\apple\AppleEatForm;
use common\models\fruit\Apple;

/**
 * @var $modelForm AppleEatForm
 * @var $model Apple
 */
$form = ActiveForm::begin([
    'method' => 'post',
    'enableAjaxValidation' => true,
    'action' => \yii\helpers\Url::to(['/apple/eat', 'id' => $model->id]),
    'options' => [
        'class' => 'modal-content',
    ]
]); ?>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <p>Сколько вы хотите откусить от яблока?</p>
        <?=$form->field($modelForm, 'size')->textInput()?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn js__accept-modal btn-primary">Откусить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
    </div>
</div>
<?php $form::end()?>
