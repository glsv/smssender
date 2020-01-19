<?php

namespace glsv\smssender\migrations;

use yii\db\Migration;

/**
 * Class m200112_052934_create_sms_log
 */
class m200112_052934_create_sms_log extends Migration
{
    private $table = 'sms_log';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'phone' => $this->string(11)->notNull(),
            'message' => $this->string(255),
            'recipient_id' => $this->string(36)->null(),
            'recipient_name' => $this->string(255)->null(),
            'method' => $this->string(20)->notNull(),
            'provider_key' => $this->string(20)->notNull(),
            'message_id' => $this->integer()->null(),
            'last_response' => $this->string(1024)->null(),
            'operation_status' => $this->string(20)->notNull(),
            'message_status' => $this->string(20)->null(),
            'provider_message_status' => $this->string(20)->null(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'delivery_date' => $this->dateTime()->null(),
        ]);

        $this->createIndex('ind_' . $this->table . '_provider_key_message_id', $this->table, ['provider_key', 'message_id'], true);
        $this->createIndex('ind_' . $this->table . '_phone', $this->table, ['phone']);
        $this->createIndex('ind_' . $this->table . 'recipient_id', $this->table, ['recipient_id']);
        $this->createIndex('ind_' . $this->table . '_operation_status', $this->table, ['operation_status']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
