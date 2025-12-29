<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%horario_laboral}}`.
 */
class m251228_231411_create_horario_laboral_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%horario_laboral}}', [
            'id' => $this->primaryKey(),
            'dia_semana' => $this->tinyInteger()->notNull(), // 1=Lun ... 7=Dom
            'hora_inicio' => $this->time()->notNull(),
            'hora_fin' => $this->time()->notNull(),
            'activo' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_horario_dia', '{{%horario_laboral}}', 'dia_semana');
        $this->createIndex('idx_horario_activo', '{{%horario_laboral}}', 'activo');
    }

    public function safeDown()
    {
        $this->dropTable('{{%horario_laboral}}');
    }
}
