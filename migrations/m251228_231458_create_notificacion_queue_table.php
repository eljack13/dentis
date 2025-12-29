<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notificacion_queue}}`.
 */
class m251228_231458_create_notificacion_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notificacion_queue}}', [
            'id' => $this->primaryKey(),
            'cita_id' => $this->integer()->null(),
            'paciente_id' => $this->integer()->null(),
            'canal' => "ENUM('WHATSAPP','EMAIL') NOT NULL DEFAULT 'WHATSAPP'",
            'tipo' => "ENUM('CONFIRMACION','RECORDATORIO_24H','RECORDATORIO_2H','CANCELACION','REPROGRAMACION') NOT NULL",
            'mensaje' => $this->text()->notNull(),
            'scheduled_at' => $this->dateTime()->notNull(),
            'status' => "ENUM('PENDIENTE','ENVIADA','ERROR') NOT NULL DEFAULT 'PENDIENTE'",
            'error_msg' => $this->text()->null(),
            'intentos' => $this->tinyInteger()->notNull()->defaultValue(0),
            'sent_at' => $this->dateTime()->null(),
            'created_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_queue_status_time', '{{%notificacion_queue}}', ['status', 'scheduled_at']);
        $this->createIndex('idx_queue_cita', '{{%notificacion_queue}}', 'cita_id');
        $this->createIndex('idx_queue_paciente', '{{%notificacion_queue}}', 'paciente_id');

        $this->addForeignKey(
            'fk_queue_cita',
            '{{%notificacion_queue}}',
            'cita_id',
            '{{%cita}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_queue_paciente',
            '{{%notificacion_queue}}',
            'paciente_id',
            '{{%paciente}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_queue_paciente', '{{%notificacion_queue}}');
        $this->dropForeignKey('fk_queue_cita', '{{%notificacion_queue}}');
        $this->dropTable('{{%notificacion_queue}}');
    }
}
