<?php

namespace Laurent\BulletinBundle\Migrations\pdo_oci;

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
                id NUMBER(10) NOT NULL, 
                name VARCHAR2(255) NOT NULL, 
                start_date NUMBER(10) DEFAULT NULL, 
                end_date NUMBER(10) DEFAULT NULL, 
                degre NUMBER(10) DEFAULT NULL, 
                annee NUMBER(10) DEFAULT NULL, 
                ReunionParent VARCHAR2(255) NOT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            DECLARE constraints_Count NUMBER; BEGIN 
            SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count 
            FROM USER_CONSTRAINTS 
            WHERE TABLE_NAME = 'LAURENT_BULLETIN_PERIODE' 
            AND CONSTRAINT_TYPE = 'P'; IF constraints_Count = 0 
            OR constraints_Count = '' THEN EXECUTE IMMEDIATE 'ALTER TABLE LAURENT_BULLETIN_PERIODE ADD CONSTRAINT LAURENT_BULLETIN_PERIODE_AI_PK PRIMARY KEY (ID)'; END IF; END;
        ");
        $this->addSql("
            CREATE SEQUENCE LAURENT_BULLETIN_PERIODE_ID_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1
        ");
        $this->addSql("
            CREATE TRIGGER LAURENT_BULLETIN_PERIODE_AI_PK BEFORE INSERT ON LAURENT_BULLETIN_PERIODE FOR EACH ROW DECLARE last_Sequence NUMBER; last_InsertID NUMBER; BEGIN 
            SELECT LAURENT_BULLETIN_PERIODE_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; IF (
                : NEW.ID IS NULL 
                OR : NEW.ID = 0
            ) THEN 
            SELECT LAURENT_BULLETIN_PERIODE_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; ELSE 
            SELECT NVL(Last_Number, 0) INTO last_Sequence 
            FROM User_Sequences 
            WHERE Sequence_Name = 'LAURENT_BULLETIN_PERIODE_ID_SEQ'; 
            SELECT : NEW.ID INTO last_InsertID 
            FROM DUAL; WHILE (last_InsertID > last_Sequence) LOOP 
            SELECT LAURENT_BULLETIN_PERIODE_ID_SEQ.NEXTVAL INTO last_Sequence 
            FROM DUAL; END LOOP; END IF; END;
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_matiere_remarque (
                id NUMBER(10) NOT NULL, 
                periode_id NUMBER(10) DEFAULT NULL, 
                matiere_id NUMBER(10) DEFAULT NULL, 
                eleve_id NUMBER(10) DEFAULT NULL, 
                remarque CLOB DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            DECLARE constraints_Count NUMBER; BEGIN 
            SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count 
            FROM USER_CONSTRAINTS 
            WHERE TABLE_NAME = 'LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE' 
            AND CONSTRAINT_TYPE = 'P'; IF constraints_Count = 0 
            OR constraints_Count = '' THEN EXECUTE IMMEDIATE 'ALTER TABLE LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE ADD CONSTRAINT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE_AI_PK PRIMARY KEY (ID)'; END IF; END;
        ");
        $this->addSql("
            CREATE SEQUENCE LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE_ID_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1
        ");
        $this->addSql("
            CREATE TRIGGER LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE_AI_PK BEFORE INSERT ON LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE FOR EACH ROW DECLARE last_Sequence NUMBER; last_InsertID NUMBER; BEGIN 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; IF (
                : NEW.ID IS NULL 
                OR : NEW.ID = 0
            ) THEN 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; ELSE 
            SELECT NVL(Last_Number, 0) INTO last_Sequence 
            FROM User_Sequences 
            WHERE Sequence_Name = 'LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE_ID_SEQ'; 
            SELECT : NEW.ID INTO last_InsertID 
            FROM DUAL; WHILE (last_InsertID > last_Sequence) LOOP 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_REMARQUE_ID_SEQ.NEXTVAL INTO last_Sequence 
            FROM DUAL; END LOOP; END IF; END;
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
                id NUMBER(10) NOT NULL, 
                name VARCHAR2(255) NOT NULL, 
                officialName VARCHAR2(255) NOT NULL, 
                withTotal NUMBER(1) DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            DECLARE constraints_Count NUMBER; BEGIN 
            SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count 
            FROM USER_CONSTRAINTS 
            WHERE TABLE_NAME = 'LAURENT_BULLETIN_POINTDIVERS' 
            AND CONSTRAINT_TYPE = 'P'; IF constraints_Count = 0 
            OR constraints_Count = '' THEN EXECUTE IMMEDIATE 'ALTER TABLE LAURENT_BULLETIN_POINTDIVERS ADD CONSTRAINT LAURENT_BULLETIN_POINTDIVERS_AI_PK PRIMARY KEY (ID)'; END IF; END;
        ");
        $this->addSql("
            CREATE SEQUENCE LAURENT_BULLETIN_POINTDIVERS_ID_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1
        ");
        $this->addSql("
            CREATE TRIGGER LAURENT_BULLETIN_POINTDIVERS_AI_PK BEFORE INSERT ON LAURENT_BULLETIN_POINTDIVERS FOR EACH ROW DECLARE last_Sequence NUMBER; last_InsertID NUMBER; BEGIN 
            SELECT LAURENT_BULLETIN_POINTDIVERS_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; IF (
                : NEW.ID IS NULL 
                OR : NEW.ID = 0
            ) THEN 
            SELECT LAURENT_BULLETIN_POINTDIVERS_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; ELSE 
            SELECT NVL(Last_Number, 0) INTO last_Sequence 
            FROM User_Sequences 
            WHERE Sequence_Name = 'LAURENT_BULLETIN_POINTDIVERS_ID_SEQ'; 
            SELECT : NEW.ID INTO last_InsertID 
            FROM DUAL; WHILE (last_InsertID > last_Sequence) LOOP 
            SELECT LAURENT_BULLETIN_POINTDIVERS_ID_SEQ.NEXTVAL INTO last_Sequence 
            FROM DUAL; END LOOP; END IF; END;
        ");
        $this->addSql("
            CREATE TABLE laurent_bulletin_periode_eleve_matiere_point (
                id NUMBER(10) NOT NULL, 
                periode_id NUMBER(10) DEFAULT NULL, 
                matiere_id NUMBER(10) DEFAULT NULL, 
                eleve_id NUMBER(10) DEFAULT NULL, 
                total NUMBER(10) DEFAULT NULL, 
                point DOUBLE PRECISION DEFAULT NULL, 
                comportement DOUBLE PRECISION DEFAULT NULL, 
                presence DOUBLE PRECISION DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            DECLARE constraints_Count NUMBER; BEGIN 
            SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count 
            FROM USER_CONSTRAINTS 
            WHERE TABLE_NAME = 'LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT' 
            AND CONSTRAINT_TYPE = 'P'; IF constraints_Count = 0 
            OR constraints_Count = '' THEN EXECUTE IMMEDIATE 'ALTER TABLE LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT ADD CONSTRAINT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT_AI_PK PRIMARY KEY (ID)'; END IF; END;
        ");
        $this->addSql("
            CREATE SEQUENCE LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT_ID_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1
        ");
        $this->addSql("
            CREATE TRIGGER LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT_AI_PK BEFORE INSERT ON LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT FOR EACH ROW DECLARE last_Sequence NUMBER; last_InsertID NUMBER; BEGIN 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; IF (
                : NEW.ID IS NULL 
                OR : NEW.ID = 0
            ) THEN 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; ELSE 
            SELECT NVL(Last_Number, 0) INTO last_Sequence 
            FROM User_Sequences 
            WHERE Sequence_Name = 'LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT_ID_SEQ'; 
            SELECT : NEW.ID INTO last_InsertID 
            FROM DUAL; WHILE (last_InsertID > last_Sequence) LOOP 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_MATIERE_POINT_ID_SEQ.NEXTVAL INTO last_Sequence 
            FROM DUAL; END LOOP; END IF; END;
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
                id NUMBER(10) NOT NULL, 
                periode_id NUMBER(10) DEFAULT NULL, 
                divers_id NUMBER(10) DEFAULT NULL, 
                eleve_id NUMBER(10) DEFAULT NULL, 
                total NUMBER(10) DEFAULT NULL, 
                point DOUBLE PRECISION DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            DECLARE constraints_Count NUMBER; BEGIN 
            SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count 
            FROM USER_CONSTRAINTS 
            WHERE TABLE_NAME = 'LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT' 
            AND CONSTRAINT_TYPE = 'P'; IF constraints_Count = 0 
            OR constraints_Count = '' THEN EXECUTE IMMEDIATE 'ALTER TABLE LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT ADD CONSTRAINT LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT_AI_PK PRIMARY KEY (ID)'; END IF; END;
        ");
        $this->addSql("
            CREATE SEQUENCE LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT_ID_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1
        ");
        $this->addSql("
            CREATE TRIGGER LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT_AI_PK BEFORE INSERT ON LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT FOR EACH ROW DECLARE last_Sequence NUMBER; last_InsertID NUMBER; BEGIN 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; IF (
                : NEW.ID IS NULL 
                OR : NEW.ID = 0
            ) THEN 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT_ID_SEQ.NEXTVAL INTO : NEW.ID 
            FROM DUAL; ELSE 
            SELECT NVL(Last_Number, 0) INTO last_Sequence 
            FROM User_Sequences 
            WHERE Sequence_Name = 'LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT_ID_SEQ'; 
            SELECT : NEW.ID INTO last_InsertID 
            FROM DUAL; WHILE (last_InsertID > last_Sequence) LOOP 
            SELECT LAURENT_BULLETIN_PERIODE_ELEVE_POINTDIVERS_POINT_ID_SEQ.NEXTVAL INTO last_Sequence 
            FROM DUAL; END LOOP; END IF; END;
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