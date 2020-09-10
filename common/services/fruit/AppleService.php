<?php

namespace common\services\fruit;

use common\forms\fruit\AppleForm;
use common\models\fruit\Apple;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\UserException;

/**
 * Class AppleService
 * @package common\services\fruit
 * @property-read Apple $apple
 */
class AppleService extends BaseObject
{
    // Максимальное количество генерируемых яблок
    const MAX_APPLE_GENERATOR = 50;

    /** @var Apple $_apple */
    protected $_apple;

    /**
     * AppleService constructor.
     * @param Apple $apple
     * @param array $config
     */
    public function __construct(Apple $apple, array $config = [])
    {
        $this->_apple = $apple;
        parent::__construct($config);
    }

    /**
     * @return Apple
     */
    public function getApple()
    {
        return $this->_apple;
    }

    /**
     * Создание яблока
     * @param array $attributes
     * @return Apple
     * @throws Exception
     * @throws UserException
     */
    public function create(array $attributes)
    {
        $apple = $this->apple;
        if (!$apple->isNewRecord) {
            throw new Exception('Объект уже был создан');
        }

        $form = new AppleForm();
        $form->setAttributes($attributes);
        if (!$form->validate()) {
            throw new UserException(implode(PHP_EOL, $form->firstErrors));
        }

        $apple->setAttributes($form->attributes);
        $apple->setDefaultStatus();
        if (!$apple->save()) {
            throw new Exception(implode(PHP_EOL, $apple->firstErrors));
        }

        return $apple;
    }

    /**
     * Поедание яблока
     * @param array $attributes
     * @return Apple
     * @throws Exception
     * @throws UserException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function eat(array $attributes)
    {
        $apple = $this->apple;
        if (!$apple->canEat()) {
            throw new UserException('Яблоко нельзя есть');
        }

        $form = new AppleForm();
        $form->attributes = $apple->attributes;
        $form->sizeBefore = round($apple->size * 100);
        $form->setAttributes($attributes);
        if (!$form->validate()) {
            throw new UserException(implode(PHP_EOL, $form->firstErrors));
        }

        $apple->setAttributes($form->attributes);
        if (!$apple->save()) {
            throw new Exception(implode(PHP_EOL, $apple->firstErrors));
        }

        if ($apple->size == 0) {
            $this->trash();
        }

        return $apple;
    }

    /**
     * Срывание/падение яблока
     * @return Apple
     * @throws Exception
     * @throws UserException
     */
    public function drop()
    {
        $apple = $this->apple;
        if (!$apple->canDrop()) {
            throw new UserException('Яблоко нельзя сорвать');
        }

        $apple->setDropped();
        $apple->dropped_at = date('Y-m-d H:i:s');
        if (!$apple->save()) {
            throw new Exception(implode(PHP_EOL, $apple->firstErrors));
        }

        return $apple;
    }

    /**
     * Выкидыание яблока
     * @return true
     * @throws Exception
     * @throws UserException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function trash()
    {
        $apple = $this->apple;
        if (!$apple->canTrash()) {
            throw new UserException('Яблоко нельзя выбросить');
        }

        if (!$apple->delete()) {
            throw new Exception(implode(PHP_EOL, $apple->firstErrors));
        }

        return true;
    }

    /**
     * Сгнивание яблока
     * @return Apple
     * @throws Exception
     * @throws UserException
     */
    public function rottedAway()
    {
        $apple = $this->apple;
        if (!$apple->canRottedAway()) {
            throw new UserException('Яблоко не может сгнить');
        }

        $apple->setRottedAway();
        $apple->rotted_away_at = date('Y-m-d H:i:s');
        if (!$apple->save()) {
            throw new Exception(implode(PHP_EOL, $apple->firstErrors));
        }

        return $apple;
    }

    /**
     * Разложение яблока
     * @return true
     * @throws Exception
     * @throws UserException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function decay()
    {
        $apple = $this->apple;
        if (!$apple->canDecay()) {
            throw new UserException('Яблоко еще не разложилось');
        }

        if (!$apple->delete()) {
            throw new Exception(implode(PHP_EOL, $apple->firstErrors));
        }

        return true;
    }

    /**
     * Генерация рандомного кличества яблок
     * @return int
     * @throws \yii\db\Exception
     */
    public static function generateApples()
    {
        $generateTo = rand(1, self::MAX_APPLE_GENERATOR);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            for ($i = 0; $i < $generateTo; $i++) {
                $apple = new Apple();
                $data = [
                    'color' => self::getRandomColor(),
                    'size' => Apple::getDefaultSize()
                ];

                $appleService = new static($apple);
                $appleService->create($data);
            }
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            $transaction->rollBack();
            throw $e;
        }

        $transaction->commit();
        return $generateTo;
    }

    /**
     * Получение рандомного цвета
     * @return mixed
     */
    protected static function getRandomColor()
    {
        $colors = array_keys(Apple::getColorLabels());
        $totalColors = count($colors);
        return $colors[rand(0, $totalColors - 1)];
    }
}
