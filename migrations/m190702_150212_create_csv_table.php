<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%csv}}`.
 */
class m190702_150212_create_csv_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%csv}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(25),
            'value' => $this->integer(11),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%csv}}');
    }
}
