<?php

namespace Laurent\BulletinBundle\Migrations\sqlsrv;

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
            ALTER TABLE laurent_bulletin_periode ALTER COLUMN start_date DATETIME2(6)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode ALTER COLUMN end_date DATETIME2(6)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode ALTER COLUMN start_date INT
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode ALTER COLUMN end_date INT
        ");
    }
}