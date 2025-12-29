<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bloqueo_agenda}}`.
 */
class m251228_231418_create_bloqueo_agenda_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bloqueo_agenda}}', [
            'id' => $this->primaryKey(),
            'titulo' => $this->string(140)->notNull(),
            'inicio' => $this->dateTime()->notNull(),
            'fin' => $this->dateTime()->notNull(),
            'motivo' => $this->text()->null(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_bloqueo_rango', '{{%bloqueo_agenda}}', ['inicio', 'fin']);
        $this->createIndex('idx_bloqueo_created_by', '{{%bloqueo_agenda}}', 'created_by');

        $this->addForeignKey(
            'fk_bloqueo_agenda_created_by',
            '{{%bloqueo_agenda}}',
            'created_by',
            '{{%usuario}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_bloqueo_agenda_created_by', '{{%bloqueo_agenda}}');
        $this->dropTable('{{%bloqueo_agenda}}');
    }
}
