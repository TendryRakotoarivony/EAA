-- 1. Insertion des fonctions (depuis missions.txt)
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
-- Sources: [3, 5]

-- 2. Insertion de 2 employés (depuis employes.txt)
INSERT INTO employe (id, matricule, id_fonction, nom, prenoms, classification, groupe, departement, service, region, lieu, date_embauche, anciannete) VALUES 
(1, 2, 1, 'RAHANITRARIVO', 'Haingotiana Isabelle', '4A', 'G3', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-02-07', 6.0),
(2, 3, 2, 'RANDRIANARISOA', 'Gilbert', 'OS3', 'G2', 'Green', 'Economie vert', 'ANALAMANGA', 'Andramasina', '2020-03-02', 6.0);
-- Sources: [1, 2, 6, 7]

-- 3. Insertion d'un entretien pour chaque employé
-- Les dates de signature sont obligatoires (NOT NULL)
INSERT INTO entretien (id, id_employe, date_entretien, niveau, date_signature_colab, date_signature_manager) VALUES 
(1, 1, '2025-03-01', 3, '2025-03-01', '2025-03-02'),
(2, 2, '2025-03-05', 4, '2025-03-05', '2025-03-06');
-- Sources: [2]

-- 4. Insertion de 6 notes de performance par entretien (Questions 1 à 6)
-- Pour l'entretien 1
INSERT INTO note_performance (id_entretien, num_question, note) VALUES 
(1, 1, 4), (1, 2, 3), (1, 3, 5), (1, 4, 4), (1, 5, 2), (1, 6, 4);
-- Pour l'entretien 2
INSERT INTO note_performance (id_entretien, num_question, note) VALUES 
(2, 1, 5), (2, 2, 4), (2, 3, 4), (2, 4, 5), (2, 5, 3), (2, 6, 5);
-- Sources: [4, 8]

-- 5. Insertion de 8 réponses QCM par entretien (Questions 1 à 8, valeurs 1 à 4)
-- Pour l'entretien 1
INSERT INTO reponse_qcm (id_entretien, num_question, reponse) VALUES 
(1, 1, 1), (1, 2, 2), (1, 3, 3), (1, 4, 4), (1, 5, 1), (1, 6, 2), (1, 7, 3), (1, 8, 4);
-- Pour l'entretien 2
INSERT INTO reponse_qcm (id_entretien, num_question, reponse) VALUES 
(2, 1, 4), (2, 2, 3), (2, 3, 2), (2, 4, 1), (2, 5, 4), (2, 6, 3), (2, 7, 2), (2, 8, 1);
-- Sources: [4, 8]

-- 6. Insertion de 2 axes de progrès par entretien
INSERT INTO axe_progres (id_entretien, label, description) VALUES 
(1, 'Communication digitale', 'Améliorer l''utilisation des outils de rapportage via tablette'),
(1, 'Médiation', 'Développer des techniques de gestion de conflits communautaires'),
(2, 'Gestion d''équipe', 'Déléguer davantage la préparation des sols'),
(2, 'Technique', 'Se former aux nouvelles essences forestières introduites');
-- Sources: [1, 8]

-- 7. Insertion de 2 formations par entretien
-- Priorité : 1:Urgente, 2:Importante, 3:Souhaitable
-- Demandeur : 1:Collaborateur, 2:Manager, 3:Les deux
INSERT INTO formation (id_entretien, titre, priorite, demandeur) VALUES 
(1, 'Communication Non-Violente', 2, 1),
(1, 'Outils de collecte de données Kobo', 1, 3),
(2, 'Management de pépinière niveau 2', 2, 2),
(2, 'Sécurité et hygiène sur site', 1, 3);
-- Sources: [3, 8]