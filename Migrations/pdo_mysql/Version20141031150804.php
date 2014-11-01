<?php

namespace Laurent\BulletinBundle\Migrations\pdo_mysql;

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
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                start_date INT DEFAULT NULL, 
                end_date INT DEFAULT NULL, 
                degre INT DEFAULT NULL, 
                annee INT DEFAULT NULL, 
                ReunionParent VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_matiere_remarque (
                id INT AUTO_INCREMENT NOT NULL, 
                periode_id INT DEFAULT NULL, 
                matiere_id INT DEFAULT NULL, 
                eleve_id INT DEFAULT NULL, 
                remarque LONGTEXT DEFAULT NULL, 
                INDEX IDX_893C9E90F384C1CF (periode_id), 
                INDEX IDX_893C9E90F46CD258 (matiere_id), 
                INDEX IDX_893C9E90A6CC7B2 (eleve_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_pointDivers (
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                officialName VARCHAR(255) NOT NULL, 
                withTotal TINYINT(1) DEFAULT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_matiere_point (
                id INT AUTO_INCREMENT NOT NULL, 
                periode_id INT DEFAULT NULL, 
                matiere_id INT DEFAULT NULL, 
                eleve_id INT DEFAULT NULL, 
                total INT DEFAULT NULL, 
                point DOUBLE PRECISION DEFAULT NULL, 
                comportement DOUBLE PRECISION DEFAULT NULL, 
                presence DOUBLE PRECISION DEFAULT NULL, 
                INDEX IDX_F00698B2F384C1CF (periode_id), 
                INDEX IDX_F00698B2F46CD258 (matiere_id), 
                INDEX IDX_F00698B2A6CC7B2 (eleve_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_pointdivers_point (
                id INT AUTO_INCREMENT NOT NULL, 
                periode_id INT DEFAULT NULL, 
                divers_id INT DEFAULT NULL, 
                eleve_id INT DEFAULT NULL, 
                total INT DEFAULT NULL, 
                point DOUBLE PRECISION DEFAULT NULL, 
                INDEX IDX_3546957CF384C1CF (periode_id), 
                INDEX IDX_3546957C9C3BA491 (divers_id), 
                INDEX IDX_3546957CA6CC7B2 (eleve_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
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
            DROP FOREIGN KEY FK_893C9E90F384C1CF
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_matiere_point 
            DROP FOREIGN KEY FK_F00698B2F384C1CF
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            DROP FOREIGN KEY FK_3546957CF384C1CF
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_pointdivers_point 
            DROP FOREIGN KEY FK_3546957C9C3BA491
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