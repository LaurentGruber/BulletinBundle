<?php

namespace Laurent\BulletinBundle\Migrations\ibm_db2;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2014/11/01 10:10:09
 */
class Version20141101221007 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            ADD COLUMN \"position\" INTEGER DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            DROP COLUMN \"position\"
        ");
    }
}