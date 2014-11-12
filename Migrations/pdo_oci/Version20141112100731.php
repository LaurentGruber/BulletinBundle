<?php

namespace Laurent\BulletinBundle\Migrations\pdo_oci;

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
            ALTER TABLE laurent_bulletin_periode MODIFY (
                start_date TIMESTAMP(0) DEFAULT NULL, 
                end_date TIMESTAMP(0) DEFAULT NULL
            )
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode MODIFY (
                start_date NUMBER(10) DEFAULT NULL, 
                end_date NUMBER(10) DEFAULT NULL
            )
        ");
    }
}