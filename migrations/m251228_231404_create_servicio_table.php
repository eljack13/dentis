<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%servicio}}`.
 */
class m251228_231404_create_servicio_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%servicio}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(120)->notNull(),
            'descripcion' => $this->text()->null(),
            'duracion_min' => $this->smallInteger()->notNull(),
            'buffer_min' => $this->smallInteger()->notNull()->defaultValue(0),
            'color' => $this->string(10)->null(),
            'activo' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_servicio_activo', '{{%servicio}}', 'activo');
    }

    public function safeDown()
    {
        $this->dropTable('{{%servicio}}');
    }
}
