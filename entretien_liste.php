<?php
try {
    include('db_connection.php');
    $db = getConnection();
    $sql = "SELECT * FROM liste_entretien";
    $entretiens = $db->query($sql)->fetchAll();
  
} catch (\Throwable $th) {
    die('Erreur lors de la récupération des entretiens : ' . $th->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entretien Annuel d'Activité — Bôndy International</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>

<body>
    <header class="header">
        <div class="header-brand">
            <div class="brand-logo">
                <img src="logo-bondy.png" alt="Logo Bôndy International">
            </div>
            <div class="brand-text">
                <div class="brand-name">Bôndy International</div>
                <div class="brand-sub">Direction des Ressources Humaines</div>
            </div>
        </div>

        <div class="header-title">
            <h1>Entretien Annuel d'Activité</h1>
            <p>Liste des évaluations annuelles — Confidentiel</p>
        </div>

        <div class="header-badge">
            <span>Année évaluée</span>
            <strong id="annee-label">2025</strong>
        </div>
    </header>

    <!-- FILTRE -->
    <div class="legend-bar">
        <span
            style="font-size:12px;font-weight:600;color:var(--muted);letter-spacing:0.06em;text-transform:uppercase;">Filtre
            :</span>
        <div class="legend-item">
            <div class="legend-dot manager"></div> À remplir par le Manager
        </div>
        <div class="legend-item">
            <div class="legend-dot collab"></div> À remplir par le Collaborateur
        </div>
        <div class="legend-item">
            <div class="legend-dot auto"></div> Rempli automatiquement
        </div>
    </div>

    <table class="perf-table">
        <thead>
            <tr>
                <th>Date de l'entretien</th>
                <th>Matricule</th>
                <th>Nom & Prenom</span>
                <th>Manager</th>
                <th>Fonction</th>
                <th>Note Moyenne de la Performance</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="perf-rows">
            <!-- Row template -->
            <?php foreach ($entretiens as $ent) { ?>
                <tr>
                    <td><?= $ent['date_entretien'] ?></td>
                    <td><?= $ent['matricule'] ?></td>
                    <td style="font-weight:500;"><?= $ent['nom_complet'] ?></td>
                    <td><?= $ent['nom_manager'] ?></td>
                    <td><?= $ent['fonction'] ?></td>
                    <td><?= isset($ent['note_moyenne']) ? number_format($ent['note_moyenne'], 2, ',', '') : 'N/A' ?></td>
                    <td>
                        <a href="./entretien_detail.php?id=<?= $ent['id'] ?>">
                            <button class="btn btn-view btn-primary">Voir</button>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>