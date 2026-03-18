<?php
try {
    include('db_connection.php');
    $db = getConnection();

    $matricule = isset($_GET['matricule']) ? trim($_GET['matricule']) : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $fonction = isset($_GET['fonction']) ? trim($_GET['fonction']) : '';
    $items_per_page = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 50;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
    $sort_dir = isset($_GET['sort_dir']) ? $_GET['sort_dir'] : 'ASC';

    // Ensure items_per_page is one of the allowed values
    if (!in_array($items_per_page, [10, 20, 50])) {
        $items_per_page = 50;
    }

    // Validate sort parameters
    $allowed_sort_columns = ['date_entretien', 'nom_complet', 'nom_manager', 'fonction', 'note_moyenne'];
    if (!in_array($sort_by, $allowed_sort_columns)) {
        $sort_by = '';
    }
    if (!in_array($sort_dir, ['ASC', 'DESC'])) {
        $sort_dir = 'ASC';
    }

    // Build WHERE clause
    $where_clause = "";
    $bindings = [];
    
    if($matricule || $search || $fonction) {
        $where_clause = " WHERE 1=1";
        if($matricule) {
            $where_clause .= " AND matricule LIKE :matricule";
            $bindings[':matricule'] = $matricule . '%';
        }
        if($search) {
            $where_clause .= " AND (LOWER(nom_complet) LIKE LOWER(:search) OR LOWER(nom_manager) LIKE LOWER(:search))";
            $bindings[':search'] = '%' . $search . '%';
        }
        if($fonction) {
            $where_clause .= " AND id_fonction = :fonction";
            $bindings[':fonction'] = $fonction;
        }
    }

    // Build ORDER BY clause
    $order_clause = '';
    if ($sort_by) {
        $order_clause = " ORDER BY " . $sort_by . " " . $sort_dir;
    }

    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM liste_entretien" . $where_clause;
    $count_stmt = $db->prepare($count_sql);
    foreach ($bindings as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_count = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_count / $items_per_page);

    // Ensure page is within valid range
    if ($page > $total_pages && $total_pages > 0) {
        $page = $total_pages;
    }

    // Calculate offset
    $offset = ($page - 1) * $items_per_page;

    // Get paginated results
    $sql = "SELECT * FROM liste_entretien" . $where_clause . $order_clause . " LIMIT :limit OFFSET :offset";
    $stmt = $db->prepare($sql);
    foreach ($bindings as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $entretiens = $stmt->fetchAll();

    $sql_fonction = "SELECT * FROM fonction";
    $fonctions = $db->query($sql_fonction)->fetchAll();
} catch (\Throwable $th) {
    die('Erreur lors de la récupération des entretiens : ' . $th->getMessage());
}

// Helper function to generate sort URL
function getSortUrl($column, $current_sort_by, $current_sort_dir, $base_params) {
    $new_dir = ($column == $current_sort_by && $current_sort_dir == 'ASC') ? 'DESC' : 'ASC';
    return '?' . http_build_query(array_merge($base_params, [
        'sort_by' => $column,
        'sort_dir' => $new_dir,
        'page' => 1
    ]));
}

// Helper function to get sort indicator
function getSortIndicator($column, $current_sort_by, $current_sort_dir) {
    if ($column == $current_sort_by) {
        return $current_sort_dir == 'ASC' ? ' ↑' : ' ↓';
    }
    return '';
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
    <div class="legend-bar legend-filter">
        <form class="filter-form" action="entretien_liste.php" method="GET">
            <div class="grid-4">
                <div class="field-group">
                    <input type="text" name="matricule" value="<?= $matricule ?? '' ?>" placeholder="Matricule">
                </div>
                <div class="field-group">
                    <input type="text" name="search" value="<?= $search ?? '' ?>" placeholder="Nom employe ou manager">
                </div>
                <div class="field-group">
                    <select name="fonction" value="<?= $fonction ?? '' ?>">
                        <option value="">Toutes les fonctions</option>
                        <?php foreach ($fonctions as $fonction) { ?>
                            <option value="<?= $fonction['id'] ?>"><?= $fonction['label'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="field-group">
                    <button class="btn btn-primary" type="submit">Recherche</button>
                </div>
            </div>
        </form>
    </div>

    <!-- TOP PAGINATION -->
    <div class="pagination-bar">
        <div class="pagination-items">
            <label for="items-per-page">Entrées par page:</label>
            <select id="items-per-page" name="items_per_page" onchange="updateItemsPerPage(this.value)">
                <option value="10" <?= $items_per_page == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $items_per_page == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $items_per_page == 50 ? 'selected' : '' ?>>50</option>
            </select>
        </div>
        <div class="pagination-info">
            <?= $total_count > 0 ? 'Affichage ' . ($offset + 1) . ' à ' . min($offset + $items_per_page, $total_count) . ' sur ' . $total_count . ' entrées' : 'Aucune entrée' ?>
        </div>
    </div>

    <table class="perf-table">
        <thead>
            <tr>
                <th><a href="<?= getSortUrl('date_entretien', $sort_by, $sort_dir, ['matricule' => $matricule, 'search' => $search, 'fonction' => $fonction, 'items_per_page' => $items_per_page]) ?>" class="sort-header">Date de l'entretien<?= getSortIndicator('date_entretien', $sort_by, $sort_dir) ?></a></th>
                <th>Matricule</th>
                <th><a href="<?= getSortUrl('nom_complet', $sort_by, $sort_dir, ['matricule' => $matricule, 'search' => $search, 'fonction' => $fonction, 'items_per_page' => $items_per_page]) ?>" class="sort-header">Nom & Prenom<?= getSortIndicator('nom_complet', $sort_by, $sort_dir) ?></a></th>
                <th><a href="<?= getSortUrl('nom_manager', $sort_by, $sort_dir, ['matricule' => $matricule, 'search' => $search, 'fonction' => $fonction, 'items_per_page' => $items_per_page]) ?>" class="sort-header">Manager<?= getSortIndicator('nom_manager', $sort_by, $sort_dir) ?></a></th>
                <th><a href="<?= getSortUrl('fonction', $sort_by, $sort_dir, ['matricule' => $matricule, 'search' => $search, 'fonction' => $fonction, 'items_per_page' => $items_per_page]) ?>" class="sort-header">Fonction<?= getSortIndicator('fonction', $sort_by, $sort_dir) ?></a></th>
                <th><a href="<?= getSortUrl('note_moyenne', $sort_by, $sort_dir, ['matricule' => $matricule, 'search' => $search, 'fonction' => $fonction, 'items_per_page' => $items_per_page]) ?>" class="sort-header">Note Moyenne de la Performance<?= getSortIndicator('note_moyenne', $sort_by, $sort_dir) ?></a></th>
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

    <!-- BOTTOM PAGINATION -->
    <div class="pagination-bar pagination-bar-bottom">
        <div class="pagination-controls">
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1, 'items_per_page' => $items_per_page, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir])) ?>" class="btn btn-secondary pagination-btn <?= $page == 1 ? 'disabled' : '' ?>">
                <span>⟨⟨</span>
            </a>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $page - 1), 'items_per_page' => $items_per_page, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir])) ?>" class="btn btn-secondary pagination-btn <?= $page == 1 ? 'disabled' : '' ?>">
                <span>⟨</span>
            </a>

            <div class="pagination-pages">
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                if ($start_page > 1) {
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1, 'items_per_page' => $items_per_page, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir])) . '" class="page-number">1</a>';
                    if ($start_page > 2) {
                        echo '<span class="page-ellipsis">...</span>';
                    }
                }
                
                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $page) {
                        echo '<span class="page-number page-current">' . $i . '</span>';
                    } else {
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i, 'items_per_page' => $items_per_page, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir])) . '" class="page-number">' . $i . '</a>';
                    }
                }
                
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="page-ellipsis">...</span>';
                    }
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages, 'items_per_page' => $items_per_page, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir])) . '" class="page-number">' . $total_pages . '</a>';
                }
                ?>
            </div>

            <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($total_pages, $page + 1), 'items_per_page' => $items_per_page, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir])) ?>" class="btn btn-secondary pagination-btn <?= $page == $total_pages || $total_pages == 0 ? 'disabled' : '' ?>">
                <span>⟩</span>
            </a>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages, 'items_per_page' => $items_per_page, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir])) ?>" class="btn btn-secondary pagination-btn <?= $page == $total_pages || $total_pages == 0 ? 'disabled' : '' ?>">
                <span>⟩⟩</span>
            </a>
        </div>
    </div>
</body>

<script>
function updateItemsPerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('items_per_page', value);
    url.searchParams.set('page', '1');
    window.location = url.toString();
}
</script>

</html>