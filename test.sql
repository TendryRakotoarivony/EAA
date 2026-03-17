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

SELECT
    entretien.*,
    employe.matricule,
    CONCAT(employe.nom, ' ', employe.prenoms) AS nom_complet,
    employe.date_embauche,
    CONCAT(employe.region, ' ', employe.lieu) AS affectation,
    fonction.label AS fonction,
    employe.anciennete,
    manager.prenoms AS nom_manager,
    f_manager.label AS fonction_manager,
    fonction.missions AS missions_fonction
FROM
    entretien
    JOIN employe ON entretien.id_employe = employe.id
    JOIN fonction ON employe.id_fonction = fonction.id
    JOIN employe as manager ON employe.id_manager = manager.id
    JOIN fonction as f_manager ON manager.id_fonction = f_manager.id
;

SELECT
    id,
    mission_ponctuelles
FROM
    detail_entretien
WHERE id = 1;