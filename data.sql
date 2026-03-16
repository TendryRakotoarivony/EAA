-- 1. Insertion des fonctions
INSERT INTO fonction (id, label, missions) VALUES 
(1, 'Médiatrice', 'Assurer la médiation entre les communautés locales et l''entreprise\nSensibiliser les populations aux activités et valeurs de Bôndy International\nCollecter et reporter les retours du terrain à la hiérarchie\nParticiper aux réunions communautaires'),
(2, 'Chef pépiniériste', 'Superviser et coordonner toutes les activités de la pépinière\nContrôler la qualité et la conformité des plants produits\nEncadrer l''équipe de pépiniéristes et planifier les tâches hebdomadaires\nAssurer le suivi des indicateurs de production'),
(3, 'Pépiniériste', 'Produire, repiquer et entretenir les plants en pépinière\nAppliquer les techniques de culture et d''arrosage adaptées\nAssurer le suivi de la croissance et signaler les anomalies\nPréparer les commandes de plants pour les livraisons'),
(4, 'Gardien', 'Assurer la surveillance et la sécurité du site 24h/24\nContrôler les entrées et sorties du personnel et des visiteurs\nSignaler tout incident ou anomalie à la hiérarchie\nTenir le registre de présence des visiteurs'),
(5, 'Agent de surface', 'Assurer le nettoyage et l''entretien quotidien des locaux\nVeiller au maintien de l''hygiène et de la propreté des espaces communs\nGérer les stocks de produits d''entretien\nSignaler les dégradations ou anomalies constatées'),
(6, 'Assistante RH', 'Gérer les dossiers administratifs du personnel\nAssurer le suivi des contrats, congés et absences\nPréparer les éléments variables de paie\nAssister la direction RH dans les recrutements et entretiens'),
(7, 'Assistante comptable', 'Saisir et contrôler les pièces comptables\nSuivre les règlements fournisseurs et clients\nAssister à la préparation des bilans et reportings financiers\nClasser et archiver les documents comptables'),
(8, 'Assistant Polyvalent', 'Assurer le support administratif et logistique au quotidien\nGérer le courrier entrant et sortant\nCoordiner les déplacements et rendez-vous\nParticiper à diverses missions transversales selon les besoins'),
(9, 'Agent polyvalent de maintenance et de surveillance', 'Effectuer les travaux de maintenance préventive et corrective\nSurveiller les installations techniques du site\nIntervenir rapidement en cas de panne ou dysfonctionnement\nTenir à jour le carnet de maintenance'),
(10, 'Skipper', 'Assurer la navigation sécurisée des embarcations\nEntretenir et vérifier le matériel nautique avant chaque sortie\nRespect des règles de sécurité maritime\nEncadrer les passagers et l''équipage');

-- 2. Insertion de 4 employés (2 managers, 2 non-managers)
-- Manager 1 : Gilbert (Chef pépiniériste)
INSERT INTO employe (id, matricule, id_manager, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciennete) VALUES 
(1, 3, NULL, 1, 'RANDRIANARISOA', 'Gilbert', 'OS3', 'G2', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-03-02', 6.0);

-- Manager 2 : Lovasoa (Assistante RH) - gérée par Gilbert
INSERT INTO employe (id, matricule, id_manager, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciennete) VALUES 
(2, 147, 1, 2, 'RAKOTONJANAHARY', 'Lovasoa', '5A', 'G3', 'Ressources Humaines', 'Ressources Humaines', 'ANALAMANGA', 'Siège', '2024-12-03', 1.2);

-- Employé non-manager 1 : Haingotiana (Médiatrice) - gérée par Lovasoa
INSERT INTO employe (id, matricule, id_manager, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciennete) VALUES 
(3, 2, 2, 3, 'RAHANITRARIVO', 'Haingotiana Isabelle', '4A', 'G3', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-02-07', 6.0);

-- Employé non-manager 2 : Heriniaina (Pépiniériste) - géré par Lovasoa
INSERT INTO employe (id, matricule, id_manager, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciennete) VALUES 
(4, 4, 2, 4, 'RANDRIANARISON', 'Heriniaina Tolojanahary', 'M2', 'G1', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-03-02', 6.0);
-- [5-9]

-- 3. Entretiens pour les non-managers uniquement (ID 3 et 4)
INSERT INTO entretien (id, id_employe, date_entretien, niveau, date_signature_colab, date_signature_manager) VALUES 
(1, 3, '2025-03-01', 3, '2025-03-01', '2025-03-02'),
(2, 4, '2025-03-05', 4, '2025-03-05', '2025-03-06');
-- [6]

-- 4. Détails pour l'entretien 1 (Médiatrice)
INSERT INTO note_performance (id_entretien, num_question, note) VALUES (1,1,4), (1,2,3), (1,3,5), (1,4,4), (1,5,2), (1,6,4);
INSERT INTO reponse_qcm (id_entretien, num_question, reponse) VALUES (1,1,1), (1,2,2), (1,3,3), (1,4,4), (1,5,1), (1,6,2), (1,7,3), (1,8,4);
INSERT INTO axe_progres (id_entretien, label, description) VALUES (1, 'Reporting', 'Améliorer la précision des collectes de retours terrain'), (1, 'Sensibilisation', 'Augmenter la fréquence des réunions communautaires');
INSERT INTO formation (id_entretien, titre, priorite, demandeur) VALUES (1, 'Médiation de crise', 1, 3), (1, 'Outils digitaux RH', 2, 1);
-- [1, 10]

-- 5. Détails pour l'entretien 2 (Pépiniériste)
INSERT INTO note_performance (id_entretien, num_question, note) VALUES (2,1,5), (2,2,4), (2,3,4), (2,4,3), (2,5,5), (2,6,4);
INSERT INTO reponse_qcm (id_entretien, num_question, reponse) VALUES (2,1,4), (2,2,3), (2,3,2), (2,4,1), (2,5,4), (2,6,3), (2,7,2), (2,8,1);
INSERT INTO axe_progres (id_entretien, label, description) VALUES (2, 'Qualité', 'Diminuer le taux de perte lors du repiquage'), (2, 'Suivi', 'Mieux documenter les anomalies de croissance');
INSERT INTO formation (id_entretien, titre, priorite, demandeur) VALUES (2, 'Techniques d''arrosage optimisées', 2, 2), (2, 'Gestion des stocks de plants', 3, 1);
-- [1, 10]