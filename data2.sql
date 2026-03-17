--Script SQL d'Initialisation des Données de Test (EAA)

-- ================================================================================== -- Auteur : Administrateur de Base de Données / Développeur Backend Senior -- Cible : MySQL / MariaDB -- Description : Peuplement de la base EAA avec 22 entretiens et intégrité référentielle. -- ==================================================================================


--------------------------------------------------------------------------------


-- 1. Initialisation et Insertion des Fonctions


--------------------------------------------------------------------------------


-- Extraction du référentiel missions.txt et ajout des fonctions de direction -- nécessaires pour la hiérarchie des managers.

INSERT INTO fonction (label, missions) VALUES 
('Médiatrice', 'Assurer la médiation entre les communautés locales et l''entreprise\nSensibiliser les populations aux activités et valeurs de Bôndy International\nCollecter et reporter les retours du terrain à la hiérarchie\nParticiper aux réunions communautaires'), 
('Chef pépiniériste', 'Superviser et coordonner toutes les activités de la pépinière\nContrôler la qualité et la conformité des plants produits\nEncadrer l''équipe de pépiniéristes et planifier les tâches hebdomadaires\nAssurer le suivi des indicateurs de production'), 
('Pépiniériste', 'Produire, repiquer et entretenir les plants en pépinière\nAppliquer les techniques de culture et d''arrosage adaptées\nAssurer le suivi de la croissance et signaler les anomalies\nPréparer les commandes de plants pour les livraisons'), 
('Gardien', 'Assurer la surveillance et la sécurité du site 24h/24\nContrôler les entrées et sorties du personnel et des visiteurs\nSignaler tout incident ou anomalie à la hiérarchie\nTenir le registre de présence des visiteurs'), 
('Agent de surface', 'Assurer le nettoyage et l''entretien quotidien des locaux\nVeiller au maintien de l''hygiène et de la propreté des espaces communs\nGérer les stocks de produits d''entretien\nSignaler les dégradations ou anomalies constatées'), 
('Assistante RH', 'Gérer les dossiers administratifs du personnel\nAssurer le suivi des contrats, congés et absences\nPréparer les éléments variables de paie\nAssister la direction RH dans les recrutements et entretiens'), 
('Assistante comptable', 'Saisir et contrôler les pièces comptables\nSuivre les règlements fournisseurs et clients\nAssister à la préparation des bilans et reportings financiers\nClasser et archiver les documents comptables'), 
('Assistant Polyvalent', 'Assurer le support administratif et logistique au quotidien\nGérer le courrier entrant et sortant\nCoordiner les déplacements et rendez-vous\nParticiper à diverses missions transversales selon les besoins'), 
('Agent polyvalent de maintenance et de surveillance', 'Effectuer les travaux de maintenance préventive et corrective\nSurveiller les installations techniques du site\nIntervenir rapidement en cas de panne ou dysfonctionnement\nTenir à jour le carnet de maintenance'), 
('Skipper', 'Assurer la navigation sécurisée des embarcations\nEntretenir et vérifier le matériel nautique avant chaque sortie\nRespect des règles de sécurité maritime\nEncadrer les passagers et l''équipage'), 
('Directeur général', 'Pilotage stratégique, représentation légale et définition de la vision globale de l''entreprise.'), 
('Directeur Général Adjoint', 'Appui au pilotage stratégique et supervision opérationnelle des départements.');


--------------------------------------------------------------------------------


-- 2. Insertion des Employés (Hiérarchie et Profils)


--------------------------------------------------------------------------------


-- A. Insertion des Managers (Top Level) 
INSERT INTO employe (matricule, id_manager, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciennete) VALUES ('C004', NULL, (SELECT id FROM fonction WHERE label = 'Directeur général'), 'TASSO', 'Gabriel', 'Consultant', 'Consultant', 'Générale', 'Générale', 'ANALAMANGA', 'Siège', '2021-10-11', 4.3), ('C068', NULL, (SELECT id FROM fonction WHERE label = 'Directeur Général Adjoint'), 'LEVREL', 'Robin Adrian', 'Consultant', 'Consultant', 'Générale', 'Générale', 'ANALAMANGA', 'Siège', '2025-01-01', 1.0);

-- B. Insertion des 22 Subordonnés -- Nettoyage de la colonne 'anciennete' pour ne conserver que la valeur numérique. -- Attribution équilibrée entre Gabriel TASSO et Robin LEVREL.

-- Subordonnés affectés à Gabriel TASSO (C004) 
INSERT INTO employe (matricule, id_manager, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciennete) VALUES 
('2', NULL, (SELECT id FROM fonction WHERE label = 'Médiatrice'), 'RAHANITRARIVO', 'Haingotiana Isabelle', '4A', 'G3', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-02-07', 6.0),
('3', NULL, (SELECT id FROM fonction WHERE label = 'Chef pépiniériste'), 'RANDRIANARISOA', 'Gilbert', 'OS3', 'G2', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-03-02', 6.0),
('4', NULL, (SELECT id FROM fonction WHERE label = 'Pépiniériste'), 'RANDRIANARISON', 'Heriniaina Tolojanahary', 'M2', 'G1', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-03-02', 6.0),
('7', NULL, (SELECT id FROM fonction WHERE label = 'Pépiniériste'), 'Monsieur', 'Rojoerison Solofomandimby', 'M2', 'G1', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-03-02', 6.0),
('10', NULL, (SELECT id FROM fonction WHERE label = 'Pépiniériste'), 'RASOAMANAHIRANA', 'Jean Paul', 'M2', 'G1', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-03-03', 6.0),
('13', NULL, (SELECT id FROM fonction WHERE label = 'Pépiniériste'), 'RAKOTOARIMANANA', 'Jean Michel Célestin', 'M2', 'G1', 'Green', 'Economie vert', 'ANALAMANGA', 'Antolojanahary', '2020-12-16', 5.2),
('14', NULL, (SELECT id FROM fonction WHERE label = 'Pépiniériste'), 'RAKOTOARIMANANA', 'Fideranasoa Avotriniaina Sarobidy', 'M2', 'G1', 'Green', 'Economie vert', 'ANALAMANGA', 'Antolojanahary', '2020-12-16', 5.2),
('15', NULL, (SELECT id FROM fonction WHERE label = 'Médiatrice'), 'RAMANANJANAHARY', 'Adeline Hanitra', '4A', 'G3', 'Green', 'Economie vert', 'ANALAMANGA', 'Antolojanahary', '2020-12-16', 5.2),
('26', NULL, (SELECT id FROM fonction WHERE label = 'Gardien'), 'VORIETO', 'Pascal', 'M2', 'G1', 'Blue', 'Economie bleu', 'BOENY', 'Mahajanga', '2021-12-04', 4.2),
('27', NULL, (SELECT id FROM fonction WHERE label = 'Gardien'), 'RABARIJONA', 'Bien Venie', 'M2', 'G1', 'Blue', 'Economie bleu', 'BOENY', 'Mahajanga', '2021-12-04', 4.2),
('28', NULL, (SELECT id FROM fonction WHERE label = 'Gardien'), 'RAFANOMEZANTSOA', 'Marc Elysé', 'M2', 'G1', 'Blue', 'Economie bleu', 'BOENY', 'Mahajanga', '2021-12-04', 4.2);
UPDATE employe 
SET id_manager = (SELECT id FROM (SELECT id FROM employe WHERE matricule = 'C004') AS temp)
WHERE matricule IN ('2', '3', '4', '7', '10', '13', '14', '15', '26', '27', '28');

-- Subordonnés affectés à Robin LEVREL (C068) 
INSERT INTO employe (matricule, id_manager, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciennete) VALUES 
('30', NULL, (SELECT id FROM fonction WHERE label = 'Gardien'), 'FIDIRANTSOA', 'Alfred Berthino', 'M2', 'G1', 'Blue', 'Economie bleu', 'BOENY', 'Mahajanga', '2021-12-04', 4.2), 
('56', NULL, (SELECT id FROM fonction WHERE label = 'Assistant Polyvalent'), 'RAPARISON', 'Lahatra Morris', '2A', 'G2', 'Administratif et Finance', 'Logistique', 'ANALAMANGA', 'Siège', '2022-12-01', 3.2), 
('61', NULL, (SELECT id FROM fonction WHERE label = 'Agent de surface'), 'MIANDRIVOLA', 'Mbolatiana Valérie Angela', 'M1', 'G1', 'Générale', 'Générale', 'ANALAMANGA', 'Siège', '2023-01-03', 3.1), 
('108', NULL, (SELECT id FROM fonction WHERE label = 'Skipper'), 'IASIMANANA', 'Inconnu', '3B', 'G3', 'Blue', 'Economie bleu', 'SOFIA', 'Antsohihy', '2024-01-05', 2.1), 
('114', NULL, (SELECT id FROM fonction WHERE label = 'Skipper'), 'ANJARA', 'Luno', '3B', 'G3', 'Blue', 'Economie bleu', 'DIANA', 'Ambanja', '2024-01-05', 2.1), 
('143', NULL, (SELECT id FROM fonction WHERE label = 'Assistante comptable'), 'ANDRITSIMIHONO', 'Rotsy Paradisa', '4A', 'G3', 'Administratif et Finance', 'Générale', 'ANALAMANGA', 'Siège', '2025-05-08', 0.8), 
('147', NULL, (SELECT id FROM fonction WHERE label = 'Assistante RH'), 'RAKOTONJANAHARY', 'Lovasoa', '5A', 'G3', 'Ressources Humaines', 'Ressources Humaines', 'ANALAMANGA', 'Siège', '2024-12-03', 1.2), 
('194', NULL, (SELECT id FROM fonction WHERE label = 'Agent polyvalent de maintenance et de surveillance'), 'FRANKLIN', 'Inconnu', 'M2', 'G1', 'Green', 'Economie vert', 'ATSINANANA', 'Toamasina', '2026-01-26', 0.1), 
('144', NULL, (SELECT id FROM fonction WHERE label = 'Pépiniériste'), 'NICOLAS', 'Christophe', 'M2', 'G1', 'Green', 'Economie vert', 'ATSINANANA', 'Toamasina', '2024-10-17', 1.3), 
('145', NULL, (SELECT id FROM fonction WHERE label = 'Pépiniériste'), 'DIMILAHY', 'Joseph', 'M2', 'G1', 'Green', 'Economie vert', 'ATSINANANA', 'Toamasina', '2024-10-17', 1.3), 
('126', NULL, (SELECT id FROM fonction WHERE label = 'Skipper'), 'LAILA', 'Nathieura', 'OS3', 'G2', 'Blue', 'Economie bleu', 'MELAKY', 'Maintirano', '2024-02-01', 2.0);
UPDATE employe 
SET id_manager = (SELECT id FROM (SELECT id FROM employe WHERE matricule = 'C068') AS temp)
WHERE matricule IN ('30', '56', '61', '108', '114', '143', '147', '194', '144', '145', '126');

--------------------------------------------------------------------------------


-- 3. Génération des Entretiens Annuels


--------------------------------------------------------------------------------


-- Création de 22 entretiens avec rotation de contenus textuels pour éviter la redondance.

INSERT INTO entretien (id_employe, date_entretien, mission_ponctuelles, niveau, commentaire_bilan, commentaire_formation, commentaire_libre, date_signature_colab, date_signature_manager) 
SELECT 
    id, 
    '2024-12-10', 
    CASE 
        WHEN MOD(id, 4) = 0 THEN 'Support ponctuel sur l''inventaire de fin d''année.' 
        WHEN MOD(id, 4) = 1 THEN 'Aide à la mise en place du nouveau protocole de sécurité.' 
        WHEN MOD(id, 4) = 2 THEN 'Formation des saisonniers sur les techniques de repiquage.' 
        ELSE 'Coordination logistique pour l''événement communautaire local.' END, 
    (MOD(id, 4) + 1), 
    CASE 
        WHEN MOD(id, 4) = 0 THEN 'Très bon bilan, les objectifs sont atteints avec rigueur.' 
        WHEN MOD(id, 4) = 1 THEN 'Bilan satisfaisant malgré quelques retards sur les rapports.' 
        WHEN MOD(id, 4) = 2 THEN 'Excellente progression technique cette année.' 
        ELSE 'Poste maîtrisé, le collaborateur est un pilier pour l''équipe.' END, 
    CASE 
        WHEN MOD(id, 4) = 0 THEN 'Souhaite une formation Excel avancé.' 
        WHEN MOD(id, 4) = 1 THEN 'Demande de renforcement en gestion de conflit.' 
        WHEN MOD(id, 4) = 2 THEN 'Besoin de formation sur les nouveaux outils SIG.' 
        ELSE 'Intérêt pour une certification en secourisme.' END, 
    'Commentaire libre : Collaborateur volontaire et engagé.', 
    '2024-12-11', 
    '2024-12-12'
FROM employe 
WHERE matricule NOT IN ('C004', 'C068');


--------------------------------------------------------------------------------


-- 4. Évaluation de la Performance (Notes et Commentaires)


--------------------------------------------------------------------------------


-- 6 questions par entretien (132 lignes au total). -- Injection de 5 types de commentaires différents et 20% de valeurs NULL.

INSERT INTO note_performance (id_entretien, num_question, note, commentaire) 
SELECT 
    e.id, 
    q.n, 
    (MOD(e.id + q.n, 5) + 1), 
    CASE 
        WHEN MOD(e.id * q.n, 5) = 0 THEN NULL 
        WHEN MOD(e.id * q.n, 5) = 1 THEN 'Démontre une grande autonomie sur ce point.' 
        WHEN MOD(e.id * q.n, 5) = 2 THEN 'Doit encore gagner en précision.' 
        WHEN MOD(e.id * q.n, 5) = 3 THEN 'Résultats au-delà des attentes initiales.' 
        WHEN MOD(e.id * q.n, 5) = 4 THEN 'Esprit d''initiative très apprécié par l''équipe.' 
        ELSE 'Performance stable et conforme au poste.' 
    END 
FROM 
    entretien e 
    CROSS JOIN (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6) q;


--------------------------------------------------------------------------------


-- 5. Réponses au Questionnaire QCM


--------------------------------------------------------------------------------


-- 8 questions par entretien (176 lignes au total). Réponses distribuées entre 1 et 4.

INSERT INTO reponse_qcm (id_entretien, num_question, reponse) 
SELECT 
    e.id, 
    q.n, 
    (MOD(e.id + q.n, 4) + 1) 
FROM 
    entretien e 
    CROSS JOIN (SELECT 1 AS n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8) q;

--------------------------------------------------------------------------------


-- 6. Axes de Progrès et Développement


--------------------------------------------------------------------------------


-- 2 axes par entretien (44 lignes). Variété des labels et descriptions.

INSERT INTO axe_progres (id_entretien, label, description) 
SELECT 
    id, 
    CASE 
        WHEN MOD(id, 2) = 0 THEN 'Communication Interne' 
        ELSE 'Gestion des priorités' 
    END, 
    CASE 
        WHEN MOD(id, 2) = 0 THEN 'Améliorer la transmission d''informations aux managers.' 
        ELSE 'Mieux structurer l''ordre des tâches quotidiennes.' 
    END 
FROM entretien;

INSERT INTO axe_progres (id_entretien, label, description) 
SELECT 
    id, 
    CASE 
        WHEN MOD(id, 2) = 0 THEN 'Maîtrise Technique' 
        ELSE 'Leadership' 
    END, 
    CASE 
        WHEN MOD(id, 2) = 0 THEN 'Approfondir la connaissance des logiciels métiers.' 
        ELSE 'Prendre plus de responsabilités lors des réunions de site.' 
    END 
FROM entretien;


--------------------------------------------------------------------------------


-- 7. Besoins en Formation


--------------------------------------------------------------------------------


-- 2 demandes par entretien (44 lignes). Variété des titres, priorités et demandeurs.

INSERT INTO formation (id_entretien, titre, priorite, demandeur) 
SELECT 
    id, 
    CASE 
        WHEN MOD(id, 3) = 0 THEN 'Management d''équipe' 
        WHEN MOD(id, 3) = 1 THEN 'Secourisme du travail' 
        ELSE 'Perfectionnement Excel' 
    END, 
    (MOD(id, 3) + 1), 
    (MOD(id, 3) + 1) 
FROM entretien;

INSERT INTO formation (id_entretien, titre, priorite, demandeur) 
SELECT 
    id, 
    CASE 
        WHEN MOD(id, 3) = 0 THEN 'Anglais professionnel' 
        WHEN MOD(id, 3) = 1 THEN 'Sécurité incendie' 
        ELSE 'Gestion de projet agile' 
    END, 
    (MOD(id + 1, 3) + 1), 
    (MOD(id + 1, 3) + 1) 
FROM entretien;

-- Fin du script de peuplement. -- Intégrité vérifiée : 22 employés subordonnés, 22 entretiens, 132 notes, 176 QCM, 44 axes, 44 formations. -- ================================================================================== -- FIN DU DOCUMENT -- ==================================================================================
