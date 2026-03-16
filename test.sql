SELECT
    entretien.id,
    entretien.date_entretien,
    employe.id AS employe_id,
    employe.matricule,
    employe.id_manager,
    employe.id_fonction,
    employe.nom,
    employe.prenoms,
    employe.classification,
    employe.groupe,
    employe.departement,
    employe.service,
    employe.region,
    employe.lieu,
    employe.date_embauche,
    employe.anciennete,
    manager.prenoms AS nom_manager,
    fonction.label AS fonction,
    (
        SELECT AVG(np.note)
        FROM note_performance np
        JOIN entretien e ON np.id_entretien = e.id
        WHERE e.id_employe = employe.id
    ) AS note_moyenne,
    (EXISTS (
        SELECT 1 
        FROM entretien 
        WHERE id_employe = employe.id)
    ) AS has_entretien
FROM 
    entretien
    JOIN employe ON entretien.id_employe = employe.id
    LEFT JOIN fonction ON employe.id_fonction = fonction.id
    LEFT JOIN employe AS manager ON employe.id_manager = manager.id
ORDER BY entretien.date_entretien DESC;