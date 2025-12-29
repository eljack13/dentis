<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%paciente}}`.
 */
class m251228_231353_create_paciente_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%paciente}}', [
            'id' => $this->primaryKey(),
            'usuario_id' => $this->integer()->null(),
            'nombre' => $this->string(120)->notNull(),
            'apellidos' => $this->string(160)->null(),
            'telefono' => $this->string(30)->notNull(),
            'email' => $this->string(120)->null(),
            'fecha_nacimiento' => $this->date()->null(),
            'notas' => $this->text()->null(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_paciente_telefono', '{{%paciente}}', 'telefono');
        $this->createIndex('idx_paciente_usuario_id', '{{%paciente}}', 'usuario_id');

        $this->addForeignKey(
            'fk_paciente_usuario',
            '{{%paciente}}',
            'usuario_id',
            '{{%usuario}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_paciente_usuario', '{{%paciente}}');
        $this->dropTable('{{%paciente}}');
    }
}
