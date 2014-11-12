<?php

namespace Laurent\BulletinBundle\Migrations\pdo_pgsql;

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
            ALTER TABLE laurent_bulletin_periode ALTER start_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode ALTER end_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode ALTER start_date TYPE INT
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode ALTER end_date TYPE INT
        ");
    }
}