<?php

namespace backend\forms\apple;

use yii\base\Model;

/**
 * Class AppleEatForm
 * @package backend\forms\apple
 */
class AppleEatForm extends Model
{
    public $size;
    public $sizeBefore;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['size', 'integer', 'min' => 1, 'max' => 100],
            [
                'size',
                'validateSize',
            ],
        ];
    }

    public function validateSize()
    {
        if (!is_null($this->sizeBefore)
            && $this->size > $this->sizeBefore
        ) {
            $this->addError('size', 'Вы не можете столько съесть');
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'size' => 'Размер укуса (%)',
        ];
    }
}
