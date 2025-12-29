<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%foto}}`.
 */
class m251228_231447_create_foto_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%foto}}', [
            'id' => $this->primaryKey(),
            'foto_sesion_id' => $this->integer()->notNull(),
            'archivo' => $this->string(255)->notNull(),
            'mime' => $this->string(60)->null(),
            'size_bytes' => $this->integer()->null(),
            'ancho' => $this->smallInteger()->null(),
            'alto' => $this->smallInteger()->null(),
            'etiqueta' => $this->string(60)->null(),
            'orden' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex('idx_foto_sesion', '{{%foto}}', 'foto_sesion_id');

        $this->addForeignKey(
            'fk_foto_foto_sesion',
            '{{%foto}}',
            'foto_sesion_id',
            '{{%foto_sesion}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_foto_foto_sesion', '{{%foto}}');
        $this->dropTable('{{%foto}}');
    }
}
