<?php

namespace backend\models;

use common\models\fruit\Apple;

/**
 * Class AppleSearch
 * @package backend\models
 */
class AppleSearch extends Apple
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'color'], 'integer'],
        ];
    }

    /**
     * @param array $params
     * @return array|Apple[]|\yii\db\ActiveRecord[]
     */
    public function search(array $params = [])
    {
        $query = Apple::find()
            ->orderBy(['id' => SORT_ASC]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andWhere('0=1');
            return $query->all();
        }

        $query->andFilterWhere([
            'status' => $this->status,
            'color' => $this->color,
        ]);

        return $query->all();
    }
}
