<?php

namespace common\models\fruit;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class Apple
 * @property int $id
 * @property int $color - Цвет
 * @property int $status - Статус
 * @property double $size - Остаток
 * @property string $dropped_at - Дата падения
 * @property string $rotted_away_at - Дата начала гниения
 * @property string $created_at - Дата создания
 * @property string $updated_at - Дата изменения
 * @property-read string $colorLabel
 * @package common\models\fruit
 */
class Apple extends ActiveRecord
{
    const
        STATUS_HANGING = 5,         // висит
        STATUS_DROPPED = 10,        // упало
        STATUS_ROTTED_AWAY = 15;    // сгнило

    const
        COLOR_RED = 5,
        COLOR_GREEN = 10,
        COLOR_YELLOW = 15,
        COLOR_ORANGE = 20;

    const ROTTED_AWAY_AFTER = 5 * 60 * 60;  // Сгниет через (сек)
    const DECAY_AFTER = 5 * 24 * 60 * 60;   // Разложется через (сек)

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'fruit.apple';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return 'NOW()';
                },
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['color', 'status', 'size'], 'required'],
            [['color', 'status'], 'integer'],
            [['size'], 'number'],
            [['dropped_at', 'rotted_away_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Цвет',
            'status' => 'Статус',
            'size' => 'Остаток',
            'dropped_at' => 'Упало',
            'rotted_away_at' => 'Начало гнить',
            'created_at' => 'Появилось',
            'updated_at' => 'Изменено',
        ];
    }

    /**
     * @return array
     */
    public static function getColorLabels()
    {
        return [
            self::COLOR_RED => 'Красное',
            self::COLOR_GREEN => 'Зеленое',
            self::COLOR_YELLOW => 'Желтое',
            self::COLOR_ORANGE => 'Оранжевое',
        ];
    }

    /**
     * @return mixed|null
     */
    public function getColorLabel()
    {
        return self::getColorLabels()[$this->color] ?? null;
    }

    /**
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_HANGING => 'Висит',
            self::STATUS_DROPPED => 'Упало',
            self::STATUS_ROTTED_AWAY => 'Сгнило',
        ];
    }

    /**
     * @return mixed|null
     */
    public function getStatusLabel()
    {
        return self::getStatusLabels()[$this->status] ?? null;
    }

    /**
     * @return bool
     */
    public function isHanging()
    {
        return $this->status == self::STATUS_HANGING;
    }

    /**
     * @return bool
     */
    public function isDropped()
    {
        return $this->status == self::STATUS_DROPPED;
    }

    /**
     * @return bool
     */
    public function isRottedAway()
    {
        return $this->status == self::STATUS_ROTTED_AWAY;
    }

    public function setHanging()
    {
        $this->status = self::STATUS_HANGING;
    }

    public function setDropped()
    {
        $this->status = self::STATUS_DROPPED;
    }

    public function setRottedAway()
    {
        $this->status = self::STATUS_ROTTED_AWAY;
    }

    /**
     * Установка начального статуса
     */
    public function setDefaultStatus()
    {
        $this->status = self::STATUS_HANGING;
    }

    /**
     * Получение базового остатка
     * @return float
     */
    public static function getDefaultSize()
    {
        return 1;
    }

    /**
     * @return array
     */
    public static function getColorHexes()
    {
        return [
            self::COLOR_RED => '#f1123b',
            self::COLOR_GREEN => '#00a65a',
            self::COLOR_YELLOW => '#fff821',
            self::COLOR_ORANGE => '#dc7205',
        ];
    }

    /**
     * @return mixed|string
     */
    public function getColorHex()
    {
        return self::getColorHexes()[$this->color] ?? '#ffffff';
    }

    /**
     * Можно ли сорвать яблоко
     * @return bool
     */
    public function canDrop()
    {
        return $this->status == self::STATUS_HANGING;
    }

    /**
     * Можно ли выкинуть яблоко
     * @return bool
     */
    public function canTrash()
    {
        return in_array(
            $this->status,
            [
                self::STATUS_DROPPED,
                self::STATUS_ROTTED_AWAY,
            ]
        );
    }

    /**
     * Можно ли съесть яблоко
     * @return bool
     */
    public function canEat()
    {
        return $this->status == self::STATUS_DROPPED;
    }

    /**
     * Может ли испортиться яблоко
     * @return bool
     */
    public function canRottedAway()
    {
        return $this->status == self::STATUS_DROPPED
            && $this->dropped_at
            && date('Y-m-d H:i:s', strtotime($this->dropped_at . ' ' . self::ROTTED_AWAY_AFTER . 'sec'))
                < date('Y-m-d H:i:s');
    }

    /**
     * Может ли разложиться яблоко
     * @return bool
     */
    public function canDecay()
    {
        return $this->status == self::STATUS_ROTTED_AWAY
            && $this->rotted_away_at
            && date('Y-m-d H:i:s', strtotime($this->rotted_away_at . ' ' . self::DECAY_AFTER . 'sec'))
                < date('Y-m-d H:i:s');
    }
}
