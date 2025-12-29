<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%usuario}}`.
 */
class m251228_231311_create_usuario_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%usuario}}', [
            'id' => $this->primaryKey(),
            'rol' => "ENUM('DENTISTA','PACIENTE') NOT NULL DEFAULT 'PACIENTE'",
            'nombre' => $this->string(120)->notNull(),
            'telefono' => $this->string(30)->null(),
            'email' => $this->string(120)->unique()->null(),
            'password_hash' => $this->string(255)->null(),
            'auth_key' => $this->string(32)->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_usuario_rol', '{{%usuario}}', 'rol');
        $this->createIndex('idx_usuario_telefono', '{{%usuario}}', 'telefono');
    }

    public function safeDown()
    {
        $this->dropTable('{{%usuario}}');
    }
}
