<?php

namespace Laurent\BulletinBundle\Migrations\drizzle_pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2014/11/12 10:07:33
 */
class Version20141112100731 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode CHANGE start_date start_date DATETIME DEFAULT NULL, 
            CHANGE end_date end_date DATETIME DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode CHANGE start_date start_date INT DEFAULT NULL, 
            CHANGE end_date end_date INT DEFAULT NULL
        ");
    }
}