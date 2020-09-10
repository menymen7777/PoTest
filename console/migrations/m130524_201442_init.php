<?php

use yii\db\Migration;
use common\models\User;

class m130524_201442_init extends Migration
{
    public function safeUp()
    {
        $this->execute('CREATE SCHEMA "user"');
        $this->createTable('{{%user.user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->integer()->notNull()->defaultValue(User::STATUS_NON_ACTIVE),
            'created_at' => 'timestamp with time zone NOT NULL DEFAULT NOW()',
            'updated_at' => 'timestamp with time zone NOT NULL DEFAULT NOW()',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user.user}}');
        $this->execute('DROP SCHEMA "user"');
    }
}
