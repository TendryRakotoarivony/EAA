<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db_connection.php';

$pdo = getConnection();

$matricule = trim($_GET['matricule'] ?? '');

if ($matricule === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Matricule manquant'
    ]);
    exit;
}

try {
    $sql = "
        SELECT 
            e.id,
            e.matricule,
            e.nom,
            e.prenoms,
            e.classification,
            e.groupe,
            e.departement,
            e.service,
            e.region,
            e.lieu,
            e.date_embauche,
            e.anciennete,
            e.id_manager,
            e.id_fonction,
            f.label AS fonction,
            f.missions,
            m.nom AS manager_nom,
            m.prenoms AS manager_prenoms,
            fm.label AS manager_fonction
        FROM employe e
        INNER JOIN fonction f ON f.id = e.id_fonction
        LEFT JOIN employe m ON m.id = e.id_manager
        LEFT JOIN fonction fm ON fm.id = m.id_fonction
        WHERE e.matricule = :matricule
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['matricule' => $matricule]);
    $emp = $stmt->fetch();

    if (!$emp) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Employé introuvable'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $emp['id'],
            'matricule' => $emp['matricule'],
            'nom' => $emp['nom'],
            'prenoms' => $emp['prenoms'],
            'nom_complet' => trim(($emp['nom'] ?? '') . ' ' . ($emp['prenoms'] ?? '')),
            'fonction' => $emp['fonction'],
            'id_fonction' => $emp['id_fonction'],
            'missions' => $emp['missions'],
            'classification' => $emp['classification'],
            'groupe' => $emp['groupe'],
            'departement' => $emp['departement'],
            'service' => $emp['service'],
            'region' => $emp['region'],
            'lieu' => $emp['lieu'],
            'date_embauche' => $emp['date_embauche'],
            'anciennete' => $emp['anciennete'],
            'manager_id' => $emp['id_manager'],
            'manager_nom_complet' => trim(($emp['manager_nom'] ?? '') . ' ' . ($emp['manager_prenoms'] ?? '')),
            'manager_fonction' => $emp['manager_fonction'] ?? ''
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}