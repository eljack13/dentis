<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%config_agenda}}`.
 */
class m251228_231424_create_config_agenda_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%config_agenda}}', [
            'id' => $this->primaryKey(),
            'slot_min' => $this->smallInteger()->notNull()->defaultValue(10),
            'min_anticipacion_horas' => $this->smallInteger()->notNull()->defaultValue(2),
            'max_anticipacion_dias' => $this->smallInteger()->notNull()->defaultValue(60),
            'permitir_domingo' => $this->tinyInteger()->notNull()->defaultValue(0),
            'zona_horaria' => $this->string(60)->notNull()->defaultValue('America/Monterrey'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        // Insertar un registro base (1 sola config)
        $now = time();
        $this->insert('{{%config_agenda}}', [
            'slot_min' => 10,
            'min_anticipacion_horas' => 2,
            'max_anticipacion_dias' => 60,
            'permitir_domingo' => 0,
            'zona_horaria' => 'America/Monterrey',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%config_agenda}}');
    }
}
