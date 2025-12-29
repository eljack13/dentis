<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%foto_sesion}}`.
 */
class m251228_231440_create_foto_sesion_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%foto_sesion}}', [
            'id' => $this->primaryKey(),
            'paciente_id' => $this->integer()->notNull(),
            'cita_id' => $this->integer()->null(), // puede no existir cita
            'fecha_sesion' => $this->date()->notNull(),
            'titulo' => $this->string(160)->null(),
            'notas' => $this->text()->null(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_foto_sesion_paciente_fecha', '{{%foto_sesion}}', ['paciente_id', 'fecha_sesion']);
        $this->createIndex('idx_foto_sesion_cita', '{{%foto_sesion}}', 'cita_id');
        $this->createIndex('idx_foto_sesion_created_by', '{{%foto_sesion}}', 'created_by');

        $this->addForeignKey(
            'fk_foto_sesion_paciente',
            '{{%foto_sesion}}',
            'paciente_id',
            '{{%paciente}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_foto_sesion_cita',
            '{{%foto_sesion}}',
            'cita_id',
            '{{%cita}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_foto_sesion_created_by',
            '{{%foto_sesion}}',
            'created_by',
            '{{%usuario}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_foto_sesion_created_by', '{{%foto_sesion}}');
        $this->dropForeignKey('fk_foto_sesion_cita', '{{%foto_sesion}}');
        $this->dropForeignKey('fk_foto_sesion_paciente', '{{%foto_sesion}}');
        $this->dropTable('{{%foto_sesion}}');
    }
}
