<?php

namespace Laurent\BulletinBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2015/06/16 03:43:12
 */
class Version20150616154308 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_decision (
                id INT AUTO_INCREMENT NOT NULL, 
                periode_id INT NOT NULL, 
                user_id INT NOT NULL, 
                decision_id INT NOT NULL, 
                INDEX IDX_8F96F04AF384C1CF (periode_id), 
                INDEX IDX_8F96F04AA76ED395 (user_id), 
                INDEX IDX_8F96F04ABDEE7539 (decision_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_decision_matieres (
                periodeelevedecision_id INT NOT NULL, 
                matiere_id INT NOT NULL, 
                INDEX IDX_747E4E6A150B1E55 (periodeelevedecision_id), 
                INDEX IDX_747E4E6AF46CD258 (matiere_id), 
                PRIMARY KEY(
                    periodeelevedecision_id, matiere_id
                )
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_decision (
                id INT AUTO_INCREMENT NOT NULL, 
                content LONGTEXT NOT NULL, 
                with_matiere TINYINT(1) NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_decision 
            ADD CONSTRAINT FK_8F96F04AF384C1CF FOREIGN KEY (periode_id) 
            REFERENCES laurent_bulletin_periode (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_decision 
            ADD CONSTRAINT FK_8F96F04AA76ED395 FOREIGN KEY (user_id) 
            REFERENCES claro_user (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_decision 
            ADD CONSTRAINT FK_8F96F04ABDEE7539 FOREIGN KEY (decision_id) 
            REFERENCES laurent_bulletin_decision (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_decision_matieres 
            ADD CONSTRAINT FK_747E4E6A150B1E55 FOREIGN KEY (periodeelevedecision_id) 
            REFERENCES laurent_bulletin_periode_eleve_decision (id) 
            ON DELETE CASCADE
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_decision_matieres 
            ADD CONSTRAINT FK_747E4E6AF46CD258 FOREIGN KEY (matiere_id) 
            REFERENCES laurent_school_matiere (id) 
            ON DELETE CASCADE
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_decision_matieres 
            DROP FOREIGN KEY FK_747E4E6A150B1E55
        ");
        $this->addSql("
            ALTER TABLE laurent_bulletin_periode_eleve_decision 
            DROP FOREIGN KEY FK_8F96F04ABDEE7539
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode_eleve_decision
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_periode_eleve_decision_matieres
        ");
        $this->addSql("
            DROP TABLE laurent_bulletin_decision
        ");
    }
}