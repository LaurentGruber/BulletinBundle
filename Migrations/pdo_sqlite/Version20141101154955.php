<?php

namespace Laurent\BulletinBundle\Migrations\pdo_sqlite;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2014/11/01 03:49:56
 */
class Version20141101154955 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_point 
            ADD COLUMN position INTEGER DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP INDEX IDX_F00698B2F384C1CF
        ");
        $this->addSql("
            DROP INDEX IDX_F00698B2F46CD258
        ");
        $this->addSql("
            DROP INDEX IDX_F00698B2A6CC7B2
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__laurent_bulletin_periode_eleve_matiere_point AS 
            SELECT id, 
            periode_id, 
            matiere_id, 
            eleve_id, 
            total, 
            point, 
            comportement, 
            presence 
            FROM laurent_bulletin_periode_eleve_matiere_point
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode_eleve_matiere_point
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_matiere_point (
                id INTEGER NOT NULL, 
                periode_id INTEGER DEFAULT NULL, 
                matiere_id INTEGER DEFAULT NULL, 
                eleve_id INTEGER DEFAULT NULL, 
                total INTEGER DEFAULT NULL, 
                point DOUBLE PRECISION DEFAULT NULL, 
                comportement DOUBLE PRECISION DEFAULT NULL, 
                presence DOUBLE PRECISION DEFAULT NULL, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_F00698B2F384C1CF FOREIGN KEY (periode_id) 
                REFERENCES laurent_bulletin_periode (id) NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_F00698B2F46CD258 FOREIGN KEY (matiere_id) 
                REFERENCES laurent_school_matiere (id) NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_F00698B2A6CC7B2 FOREIGN KEY (eleve_id) 
                REFERENCES claro_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO laurent_bulletin_periode_eleve_matiere_point (
                id, periode_id, matiere_id, eleve_id, 
                total, point, comportement, presence
            ) 
            SELECT id, 
            periode_id, 
            matiere_id, 
            eleve_id, 
            total, 
            point, 
            comportement, 
            presence 
            FROM __temp__laurent_bulletin_periode_eleve_matiere_point
        ");
        $this->addSql("
            DROP TABLE __temp__laurent_bulletin_periode_eleve_matiere_point
        ");
        $this->addSql("
            CREATE INDEX IDX_F00698B2F384C1CF ON laurent_bulletin_periode_eleve_matiere_point (periode_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_F00698B2F46CD258 ON laurent_bulletin_periode_eleve_matiere_point (matiere_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_F00698B2A6CC7B2 ON laurent_bulletin_periode_eleve_matiere_point (eleve_id)
        ");
    }
}