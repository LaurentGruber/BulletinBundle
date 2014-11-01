<?php

namespace Laurent\BulletinBundle\Migrations\pdo_sqlite;

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
            ADD COLUMN position INTEGER DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP INDEX IDX_3546957CF384C1CF
        ");
        $this->addSql("
            DROP INDEX IDX_3546957C9C3BA491
        ");
        $this->addSql("
            DROP INDEX IDX_3546957CA6CC7B2
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__laurent_bulletin_periode_eleve_pointdivers_point AS 
            SELECT id, 
            periode_id, 
            divers_id, 
            eleve_id, 
            total, 
            point 
            FROM laurent_bulletin_periode_eleve_pointdivers_point
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode_eleve_pointdivers_point
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_pointdivers_point (
                id INTEGER NOT NULL, 
                periode_id INTEGER DEFAULT NULL, 
                divers_id INTEGER DEFAULT NULL, 
                eleve_id INTEGER DEFAULT NULL, 
                total INTEGER DEFAULT NULL, 
                point DOUBLE PRECISION DEFAULT NULL, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_3546957CF384C1CF FOREIGN KEY (periode_id) 
                REFERENCES laurent_bulletin_periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_3546957C9C3BA491 FOREIGN KEY (divers_id) 
                REFERENCES laurent_bulletin_pointDivers (id) NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_3546957CA6CC7B2 FOREIGN KEY (eleve_id) 
                REFERENCES claro_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO laurent_bulletin_periode_eleve_pointdivers_point (
                id, periode_id, divers_id, eleve_id, 
                total, point
            ) 
            SELECT id, 
            periode_id, 
            divers_id, 
            eleve_id, 
            total, 
            point 
            FROM __temp__laurent_bulletin_periode_eleve_pointdivers_point
        ");
        $this->addSql("
            DROP TABLE __temp__laurent_bulletin_periode_eleve_pointdivers_point
        ");
        $this->addSql("
            CREATE INDEX IDX_3546957CF384C1CF ON laurent_bulletin_periode_eleve_pointdivers_point (periode_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_3546957C9C3BA491 ON laurent_bulletin_periode_eleve_pointdivers_point (divers_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_3546957CA6CC7B2 ON laurent_bulletin_periode_eleve_pointdivers_point (eleve_id)
        ");
    }
}