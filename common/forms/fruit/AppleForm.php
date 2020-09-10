<?php

namespace common\forms\fruit;

use common\models\fruit\Apple;
use yii\base\Model;

/**
 * Class AppleForm
 * @package common\forms\fruit
 */
class AppleForm extends Model
{
    public $color;
    public $size;

    public $sizeBefore;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['color', 'size'], 'required'],
            [['color'], 'integer'],
            [['color'], 'in', 'range' => [
                Apple::COLOR_RED,
                Apple::COLOR_GREEN,
                Apple::COLOR_YELLOW,
                Apple::COLOR_ORANGE,
            ]],
            ['size', 'number', 'min' => 0, 'max' => 1],
            [
                'size',
                'validateSize',
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'color' => 'Цвет',
            'size' => 'Остаток',
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
}
