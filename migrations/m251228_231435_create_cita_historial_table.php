<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cita_historial}}`.
 */
class m251228_231435_create_cita_historial_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cita_historial}}', [
            'id' => $this->primaryKey(),
            'cita_id' => $this->integer()->notNull(),
            'accion' => "ENUM('CREADA','CONFIRMADA','REPROGRAMADA','CANCELADA','MARCAR_ATENDIDA','MARCAR_NO_ASISTIO') NOT NULL",
            'detalle' => $this->text()->null(),
            'old_inicio' => $this->dateTime()->null(),
            'old_fin' => $this->dateTime()->null(),
            'new_inicio' => $this->dateTime()->null(),
            'new_fin' => $this->dateTime()->null(),
            'realizado_por' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_cita_historial_cita', '{{%cita_historial}}', 'cita_id');
        $this->createIndex('idx_cita_historial_accion', '{{%cita_historial}}', 'accion');

        $this->addForeignKey(
            'fk_cita_historial_cita',
            '{{%cita_historial}}',
            'cita_id',
            '{{%cita}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_cita_historial_realizado_por',
            '{{%cita_historial}}',
            'realizado_por',
            '{{%usuario}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_cita_historial_realizado_por', '{{%cita_historial}}');
        $this->dropForeignKey('fk_cita_historial_cita', '{{%cita_historial}}');
        $this->dropTable('{{%cita_historial}}');
    }
}
