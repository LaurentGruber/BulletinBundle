<?php

namespace Laurent\BulletinBundle\Migrations\pdo_ibm;

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
            ALTER TABLE laurent_bulletin_periode ALTER start_date start_date TIMESTAMP(0) DEFAULT NULL ALTER end_date end_date TIMESTAMP(0) DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode ALTER start_date start_date INTEGER DEFAULT NULL ALTER end_date end_date INTEGER DEFAULT NULL
        ");
    }
}