<?php

namespace glsv\smssender\migrations;

use yii\db\Migration;

/**
 * Class m200112_052934_create_sms_log
 */
class m200203_082531_modify_sms_log extends Migration
{
    private $table = 'sms_log';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->table, 'message_id', $this->string()->null());
        $this->dropColumn($this->table, 'delivery_date');
        $this->addColumn($this->table, 'delivered_at', $this->integer()->null());
        $this->addColumn($this->table, 'provider_status_description', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->table, 'message_id', $this->integer()->null());
        $this->dropColumn($this->table, 'delivered_at');
        $this->addColumn($this->table, 'delivery_date', $this->dateTime()->null());
        $this->dropColumn($this->table, 'provider_status_description');
    }
}
