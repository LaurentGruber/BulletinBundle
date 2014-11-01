<?php

namespace Laurent\BulletinBundle\Migrations\sqlsrv;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2014/10/31 03:08:05
 */
class Version20141031150804 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode (
                id INT IDENTITY NOT NULL, 
                name NVARCHAR(255) NOT NULL, 
                start_date INT, 
                end_date INT, 
                degre INT, 
                annee INT, 
                ReunionParent NVARCHAR(255) NOT NULL, 
                PRIMARY KEY (id)
            )
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_matiere_remarque (
                id INT IDENTITY NOT NULL, 
                periode_id INT, 
                matiere_id INT, 
                eleve_id INT, 
                remarque VARCHAR(MAX), 
                PRIMARY KEY (id)
            )
        ");
        $this->addSql("
            CREATE INDEX IDX_893C9E90F384C1CF ON laurent_bulletin_periode_eleve_matiere_remarque (periode_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_893C9E90F46CD258 ON laurent_bulletin_periode_eleve_matiere_remarque (matiere_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_893C9E90A6CC7B2 ON laurent_bulletin_periode_eleve_matiere_remarque (eleve_id)
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_pointDivers (
                id INT IDENTITY NOT NULL, 
                name NVARCHAR(255) NOT NULL, 
                officialName NVARCHAR(255) NOT NULL, 
                withTotal BIT, 
                PRIMARY KEY (id)
            )
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_matiere_point (
                id INT IDENTITY NOT NULL, 
                periode_id INT, 
                matiere_id INT, 
                eleve_id INT, 
                total INT, 
                point DOUBLE PRECISION, 
                comportement DOUBLE PRECISION, 
                presence DOUBLE PRECISION, 
                PRIMARY KEY (id)
            )
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
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_pointdivers_point (
                id INT IDENTITY NOT NULL, 
                periode_id INT, 
                divers_id INT, 
                eleve_id INT, 
                total INT, 
                point DOUBLE PRECISION, 
                PRIMARY KEY (id)
            )
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
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_remarque 
            ADD CONSTRAINT FK_893C9E90F384C1CF FOREIGN KEY (periode_id) 
            REFERENCES laurent_bulletin_periode (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_remarque 
            ADD CONSTRAINT FK_893C9E90F46CD258 FOREIGN KEY (matiere_id) 
            REFERENCES laurent_school_matiere (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_remarque 
            ADD CONSTRAINT FK_893C9E90A6CC7B2 FOREIGN KEY (eleve_id) 
            REFERENCES claro_user (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_point 
            ADD CONSTRAINT FK_F00698B2F384C1CF FOREIGN KEY (periode_id) 
            REFERENCES laurent_bulletin_periode (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_point 
            ADD CONSTRAINT FK_F00698B2F46CD258 FOREIGN KEY (matiere_id) 
            REFERENCES laurent_school_matiere (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_point 
            ADD CONSTRAINT FK_F00698B2A6CC7B2 FOREIGN KEY (eleve_id) 
            REFERENCES claro_user (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            ADD CONSTRAINT FK_3546957CF384C1CF FOREIGN KEY (periode_id) 
            REFERENCES laurent_bulletin_periode (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            ADD CONSTRAINT FK_3546957C9C3BA491 FOREIGN KEY (divers_id) 
            REFERENCES laurent_bulletin_pointDivers (id)
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            ADD CONSTRAINT FK_3546957CA6CC7B2 FOREIGN KEY (eleve_id) 
            REFERENCES claro_user (id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_remarque 
            DROP CONSTRAINT FK_893C9E90F384C1CF
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_point 
            DROP CONSTRAINT FK_F00698B2F384C1CF
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            DROP CONSTRAINT FK_3546957CF384C1CF
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            DROP CONSTRAINT FK_3546957C9C3BA491
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode_eleve_matiere_remarque
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_pointDivers
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode_eleve_matiere_point
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode_eleve_pointdivers_point
        ");
    }
}