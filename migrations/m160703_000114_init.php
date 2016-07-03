<?php

use yii\db\Migration;

class m160703_000114_init extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
            'email' => 'varchar(255) NOT NULL DEFAULT \'\'',
            'authkey' => 'varchar(255) DEFAULT NULL',
            'password_hash' => 'varchar(255) DEFAULT NULL',
            '_status' => 'varchar(255) DEFAULT NULL',
            'fname' => 'varchar(255) DEFAULT NULL',
            'lname' => 'varchar(255) DEFAULT NULL',
            'role' => 'varchar(20) DEFAULT NULL',
            'auth_key' => 'varchar(255) DEFAULT NULL',
            'PRIMARY KEY (`id`)'
        ]);

        $this->createIndex('user_email_unq', 'user', 'email', true);
        $this->createIndex('user_status_idx', 'user', '_status');
    }

    public function down()
    {
        echo "m160703_000114_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
