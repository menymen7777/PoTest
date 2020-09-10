<?php

use yii\db\Migration;

/**
 * Class m200909_151508_create_tbl_apple
 */
class m200909_151508_create_tbl_apple extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE SCHEMA fruit');
        $this->createTable('{{%fruit.apple}}', [
            'id' => $this->primaryKey(),
            'color' => $this->integer()->notNull()->comment('Цвет'),
            'status' => $this->integer()->notNull()->comment('Статус'),
            'size' => $this->double()->notNull()->comment('Остаток'),
            'dropped_at' => 'timestamp with time zone',
            'rotted_away_at' => 'timestamp with time zone',
            'created_at' => 'timestamp with time zone NOT NULL DEFAULT NOW()',
            'updated_at' => 'timestamp with time zone NOT NULL DEFAULT NOW()',
        ]);
        $this->addCommentOnColumn('{{%fruit.apple}}', 'dropped_at', 'Дата падения');
        $this->addCommentOnColumn('{{%fruit.apple}}', 'rotted_away_at', 'Дата начала гниения');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%fruit.apple}}');
        $this->execute('DROP SCHEMA fruit');
    }
}
