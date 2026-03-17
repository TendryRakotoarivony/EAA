CREATE TABLE axe_progres (
  id           int(10) NOT NULL AUTO_INCREMENT, 
  id_entretien int(10) NOT NULL, 
  label        varchar(255) NOT NULL, 
  description  text NOT NULL, 
  PRIMARY KEY (id)) ENGINE=InnoDB;
CREATE TABLE employe (
  id             int(10) NOT NULL AUTO_INCREMENT, 
  matricule      varchar(255) NOT NULL, 
  id_manager     int(10), 
  id_fonction    int(10) NOT NULL, 
  nom            varchar(255) NOT NULL, 
  prenoms        varchar(255) NOT NULL, 
  classification varchar(255) NOT NULL, 
  groupe         varchar(255) NOT NULL, 
  departement    varchar(255) NOT NULL, 
  service        varchar(255) NOT NULL, 
  region         varchar(255) NOT NULL, 
  lieu           varchar(255) NOT NULL, 
  date_embauche  date NOT NULL, 
  anciennete     numeric(10, 1) DEFAULT 0 NOT NULL, 
  PRIMARY KEY (id)) ENGINE=InnoDB;
CREATE TABLE entretien (
  id                     int(10) NOT NULL AUTO_INCREMENT, 
  id_employe             int(10) NOT NULL, 
  date_entretien         date NOT NULL, 
  mission_ponctuelles    text, 
  niveau                 int(10) comment '1:Debutant, 2:Intermediaire, 3:Confirme, 4:Senior', 
  commentaire_bilan      text, 
  commentaire_formation  text, 
  commentaire_libre      text, 
  date_signature_colab   date NOT NULL, 
  date_signature_manager date NOT NULL, 
  PRIMARY KEY (id)) ENGINE=InnoDB;
CREATE TABLE fonction (
  id       int(10) NOT NULL AUTO_INCREMENT, 
  label    varchar(255) NOT NULL, 
  missions text NOT NULL, 
  PRIMARY KEY (id)) ENGINE=InnoDB;
CREATE TABLE formation (
  id           int(10) NOT NULL AUTO_INCREMENT, 
  id_entretien int(10) NOT NULL, 
  titre        varchar(255) NOT NULL, 
  priorite     int(10) NOT NULL comment '1:Urgente, 2:Importante, 3:Souhaitable', 
  demandeur    int(10) NOT NULL comment '1:Collaborateur, 2:Manager, 3:Les deux', 
  PRIMARY KEY (id)) ENGINE=InnoDB;
CREATE TABLE note_performance (
  id           int(10) NOT NULL AUTO_INCREMENT, 
  id_entretien int(10) NOT NULL, 
  num_question int(10) NOT NULL, 
  note         int(10) NOT NULL, 
  commentaire  text,
  PRIMARY KEY (id)) ENGINE=InnoDB;
CREATE TABLE reponse_qcm (
  id           int(10) NOT NULL AUTO_INCREMENT, 
  id_entretien int(10) NOT NULL, 
  num_question int(10) NOT NULL, 
  reponse      int(10) NOT NULL, 
  PRIMARY KEY (id)) ENGINE=InnoDB;
ALTER TABLE employe ADD CONSTRAINT FKemploye375684 FOREIGN KEY (id_fonction) REFERENCES fonction (id);
ALTER TABLE employe ADD CONSTRAINT FKemploye449343 FOREIGN KEY (id_manager) REFERENCES employe (id);
ALTER TABLE entretien ADD CONSTRAINT FKentretien707493 FOREIGN KEY (id_employe) REFERENCES employe (id);
ALTER TABLE note_performance ADD CONSTRAINT FKnote_perfo709938 FOREIGN KEY (id_entretien) REFERENCES entretien (id);
ALTER TABLE axe_progres ADD CONSTRAINT FKaxe_progre272269 FOREIGN KEY (id_entretien) REFERENCES entretien (id);
ALTER TABLE reponse_qcm ADD CONSTRAINT FKreponse_qc737900 FOREIGN KEY (id_entretien) REFERENCES entretien (id);
ALTER TABLE formation ADD CONSTRAINT FKformation155155 FOREIGN KEY (id_entretien) REFERENCES entretien (id);

DROP VIEW liste_entretien;
CREATE VIEW liste_entretien AS
SELECT
    entretien.id,
    entretien.date_entretien,
    employe.id AS employe_id,
    employe.matricule,
    employe.id_manager,
    employe.id_fonction,
    CONCAT(employe.nom, ' ', employe.prenoms) AS nom_complet,
    CONCAT(manager.nom, ' ', manager.prenoms) AS nom_manager,
    fonction.label AS fonction,
    (
        SELECT AVG(np.note)
        FROM note_performance np
        JOIN entretien e ON np.id_entretien = e.id
        WHERE e.id_employe = employe.id
    ) AS note_moyenne
FROM 
    entretien
    JOIN employe ON entretien.id_employe = employe.id
    LEFT JOIN fonction ON employe.id_fonction = fonction.id
    LEFT JOIN employe AS manager ON employe.id_manager = manager.id
WHERE 
    entretien.id = (
        SELECT MAX(e2.id) 
        FROM entretien e2 
        WHERE e2.id_employe = employe.id
    )
ORDER BY entretien.date_entretien DESC, nom_complet ASC;

DROP VIEW detail_entretien;
CREATE VIEW detail_entretien AS
SELECT
    entretien.*,
    employe.matricule,
    CONCAT(employe.nom, ' ', employe.prenoms) AS nom_complet,
    employe.date_embauche,
    CONCAT(employe.region, ' ', employe.lieu) AS affectation,
    fonction.label AS fonction,
    employe.anciennete,
    CONCAT(manager.nom, ' ', manager.prenoms) AS nom_manager,
    f_manager.label AS fonction_manager,
    fonction.missions AS missions_fonction
FROM
    entretien
    JOIN employe ON entretien.id_employe = employe.id
    JOIN fonction ON employe.id_fonction = fonction.id
    JOIN employe as manager ON employe.id_manager = manager.id
    JOIN fonction as f_manager ON manager.id_fonction = f_manager.id
;
