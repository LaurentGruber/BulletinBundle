<?php

namespace Laurent\BulletinBundle\Migrations\pdo_sqlite;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2014/12/15 10:56:07
 */
class Version20141215105606 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode 
            ADD COLUMN onlyPoint BOOLEAN DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__laurent_bulletin_periode AS 
            SELECT id, 
            name, 
            start_date, 
            end_date, 
            degre, 
            annee, 
            ReunionParent, 
            template 
            FROM laurent_bulletin_periode
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode (
                id INTEGER NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                start_date DATETIME DEFAULT NULL, 
                end_date DATETIME DEFAULT NULL, 
                degre INTEGER DEFAULT NULL, 
                annee INTEGER DEFAULT NULL, 
                ReunionParent VARCHAR(255) NOT NULL, 
                template VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            INSERT INTO laurent_bulletin_periode (
                id, name, start_date, end_date, degre, 
                annee, ReunionParent, template
            ) 
            SELECT id, 
            name, 
            start_date, 
            end_date, 
            degre, 
            annee, 
            ReunionParent, 
            template 
            FROM __temp__laurent_bulletin_periode
        ");
        $this->addSql("
            DROP TABLE __temp__laurent_bulletin_periode
        ");
    }
}