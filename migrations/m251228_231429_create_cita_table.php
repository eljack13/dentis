<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cita}}`.
 */
class m251228_231429_create_cita_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cita}}', [
            'id' => $this->primaryKey(),
            'folio' => $this->string(20)->unique()->null(),
            'paciente_id' => $this->integer()->notNull(),
            'servicio_id' => $this->integer()->notNull(),
            'inicio' => $this->dateTime()->notNull(),
            'fin' => $this->dateTime()->notNull(),
            'estado' => "ENUM('PENDIENTE','CONFIRMADA','CANCELADA_PACIENTE','CANCELADA_DENTISTA','NO_ASISTIO','ATENDIDA') NOT NULL DEFAULT 'PENDIENTE'",
            'motivo_cancelacion' => $this->text()->null(),
            'canal' => "ENUM('WEB','DENTISTA') NOT NULL DEFAULT 'WEB'",
            'notas' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_cita_inicio', '{{%cita}}', 'inicio');
        $this->createIndex('idx_cita_rango', '{{%cita}}', ['inicio', 'fin']);
        $this->createIndex('idx_cita_paciente', '{{%cita}}', 'paciente_id');
        $this->createIndex('idx_cita_estado', '{{%cita}}', 'estado');

        $this->addForeignKey(
            'fk_cita_paciente',
            '{{%cita}}',
            'paciente_id',
            '{{%paciente}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_cita_servicio',
            '{{%cita}}',
            'servicio_id',
            '{{%servicio}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_cita_servicio', '{{%cita}}');
        $this->dropForeignKey('fk_cita_paciente', '{{%cita}}');
        $this->dropTable('{{%cita}}');
    }
}
