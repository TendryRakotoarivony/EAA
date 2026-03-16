<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db_connection.php';

$pdo = getConnection();

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'JSON invalide'
    ]);
    exit;
}

$idEmploye = (int)($input['id_employe'] ?? 0);
$dateEntretien = $input['date_entretien'] ?? null;

if ($idEmploye <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Employé invalide'
    ]);
    exit;
}

if (!$dateEntretien) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => "Date d'entretien manquante"
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $sqlEntretien = "
        INSERT INTO entretien (
            id_employe,
            date_entretien,
            mission_ponctuelles,
            niveau,
            commentaire_bilan,
            commentaire_formation,
            commentaire_libre,
            date_signature_colab,
            date_signature_manager
        ) VALUES (
            :id_employe,
            :date_entretien,
            :mission_ponctuelles,
            :niveau,
            :commentaire_bilan,
            :commentaire_formation,
            :commentaire_libre,
            :date_signature_colab,
            :date_signature_manager
        )
    ";

    $stmtEntretien = $pdo->prepare($sqlEntretien);
    $stmtEntretien->execute([
        'id_employe' => $idEmploye,
        'date_entretien' => $dateEntretien,
        'mission_ponctuelles' => $input['mission_ponctuelles'] ?? null,
        'niveau' => $input['niveau'] ?? null,
        'commentaire_bilan' => $input['commentaire_bilan'] ?? null,
        'commentaire_formation' => $input['commentaire_formation'] ?? null,
        'commentaire_libre' => $input['commentaire_libre'] ?? null,
        'date_signature_colab' => !empty($input['date_signature_colab']) ? $input['date_signature_colab'] : null,
        'date_signature_manager' => !empty($input['date_signature_manager']) ? $input['date_signature_manager'] : null,
    ]);

    $idEntretien = (int)$pdo->lastInsertId();

    if (!empty($input['performance']) && is_array($input['performance'])) {
        $sqlPerf = "
            INSERT INTO note_performance (
                id_entretien,
                num_question,
                note
            ) VALUES (
                :id_entretien,
                :num_question,
                :note
            )
        ";

        $stmtPerf = $pdo->prepare($sqlPerf);

        foreach ($input['performance'] as $perf) {
            $note = isset($perf['note']) ? (int)$perf['note'] : 0;
            $numQuestion = isset($perf['num_question']) ? (int)$perf['num_question'] : 0;

            if ($numQuestion <= 0 || $note <= 0) {
                continue;
            }

            $stmtPerf->execute([
                'id_entretien' => $idEntretien,
                'num_question' => $numQuestion,
                'note' => $note
            ]);
        }
    }

    if (!empty($input['qcm']) && is_array($input['qcm'])) {
        $sqlQcm = "
            INSERT INTO reponse_qcm (
                id_entretien,
                num_question,
                reponse
            ) VALUES (
                :id_entretien,
                :num_question,
                :reponse
            )
        ";

        $stmtQcm = $pdo->prepare($sqlQcm);

        foreach ($input['qcm'] as $qcm) {
            $reponse = isset($qcm['reponse']) ? (int)$qcm['reponse'] : 0;
            $numQuestion = isset($qcm['num_question']) ? (int)$qcm['num_question'] : 0;

            if ($numQuestion <= 0 || $reponse <= 0) {
                continue;
            }

            $stmtQcm->execute([
                'id_entretien' => $idEntretien,
                'num_question' => $numQuestion,
                'reponse' => $reponse
            ]);
        }
    }

    if (!empty($input['axes']) && is_array($input['axes'])) {
        $sqlAxe = "
            INSERT INTO axe_progres (
                id_entretien,
                label,
                description
            ) VALUES (
                :id_entretien,
                :label,
                :description
            )
        ";

        $stmtAxe = $pdo->prepare($sqlAxe);

        foreach ($input['axes'] as $axe) {
            $label = trim($axe['label'] ?? '');
            $description = trim($axe['description'] ?? '');

            if ($label === '' && $description === '') {
                continue;
            }

            $stmtAxe->execute([
                'id_entretien' => $idEntretien,
                'label' => $label,
                'description' => $description
            ]);
        }
    }

    if (!empty($input['formations']) && is_array($input['formations'])) {
        $sqlFormation = "
            INSERT INTO formation (
                id_entretien,
                titre,
                priorite,
                demandeur
            ) VALUES (
                :id_entretien,
                :titre,
                :priorite,
                :demandeur
            )
        ";

        $stmtFormation = $pdo->prepare($sqlFormation);

        foreach ($input['formations'] as $formation) {
            $titre = trim($formation['titre'] ?? '');
            $priorite = isset($formation['priorite']) ? (int)$formation['priorite'] : 0;
            $demandeur = isset($formation['demandeur']) ? (int)$formation['demandeur'] : 0;

            if ($titre === '') {
                continue;
            }

            $stmtFormation->execute([
                'id_entretien' => $idEntretien,
                'titre' => $titre,
                'priorite' => $priorite,
                'demandeur' => $demandeur
            ]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Entretien enregistré avec succès',
        'id_entretien' => $idEntretien
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "Erreur lors de l'enregistrement",
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}