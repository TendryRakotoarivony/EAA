<?php
try {
    include('db_connection.php');
    $db = getConnection();
    $id = $_GET['id'] ?? null;

    $sql = "SELECT * FROM detail_entretien WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id]);
    $entretien = $stmt->fetch();

    $sql_note = "SELECT num_question, note, commentaire FROM note_performance WHERE id_entretien = :id ORDER BY num_question ASC";
    $stmt_note = $db->prepare($sql_note);
    $stmt_note->execute(['id' => $id]);
    $notesData = $stmt_note->fetchAll();
    $notes = [];
    foreach ($notesData as $row) {
        $notes[$row['num_question']] = [
            'note' => $row['note'],
            'commentaire' => $row['commentaire']
        ];
    }

    $sql_axe = "SELECT * from axe_progres WHERE id_entretien = :id ORDER BY id ASC";
    $stmt_axe = $db->prepare($sql_axe);
    $stmt_axe->execute(['id' => $id]);
    $axes = $stmt_axe->fetchAll();

    $sql_qcm = "SELECT num_question, reponse FROM reponse_qcm WHERE id_entretien = :id ORDER BY num_question ASC";
    $stmt_qcm = $db->prepare($sql_qcm);
    $stmt_qcm->execute(['id' => $id]);
    $reponses = $stmt_qcm->fetchAll(PDO::FETCH_KEY_PAIR);

    $sql_formation = "SELECT * FROM formation WHERE id_entretien = :id ORDER BY id ASC";
    $stmt_formation = $db->prepare($sql_formation);
    $stmt_formation->execute(['id' => $id]);
    $formations = $stmt_formation->fetchAll();
    $priorites = [
      '1' => 'Urgent',
      '2' => 'Importante',
      '3' => 'Souhaitable',
    ];
    $demandeurs = [
      '1' => 'Collaborateur',
      '2' => 'Manager',
      '3' => 'Les deux',
    ];

} catch (\Throwable $th) {
    die('Erreur lors de la récupération de l\'entretien : ' . $th->getMessage());
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
  <script src="script_detail.js" defer></script>  
</head>

<body>

  <!-- HEADER -->
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
    <p>Détails de l'évaluation annuelle — Confidentiel</p>
  </div>

  <div class="header-badge">
    <span>Année évaluée</span>
    <strong id="annee-label">2025</strong>
  </div>
</header>

  <form id="eaa-form">
    <div class="form-body">

      <!-- ═══════════════════════════════════════════
       SECTION 0 — INFORMATIONS GÉNÉRALES
  ═══════════════════════════════════════════ -->
      <div class="section">
        <div class="section-header">
          <div class="section-number">✦</div>
          <div>
            <div class="section-title">Informations du collaborateur</div>
            <div class="section-sub">Données administratives</div>
          </div>
        </div>
        <div class="section-body">
          <div class="grid-3">
            <div class="field">
              <label>Nom & Prénom du collaborateur</label>
              <input type="text" id="inp-collab-nom" name="collab_nom" value="<?= $entretien['nom_complet'] ?>" readonly/>
            </div>
            <div class="field">
              <label>Matricule</label>
              <input type="text" id="inp-matricule" name="matricule" value="<?= $entretien['matricule'] ?>" readonly/>
            </div>
            <div class="field">
              <label>Date d'embauche</label>
              <input type="date" id="inp-date-embauche" name="date_embauche" value="<?= $entretien['date_embauche'] ?>" readonly/>
            </div>
            <div class="field">
              <label>Lieu d'affectation</label>
              <input type="text" id="inp-lieu" name="lieu" value="<?= $entretien['affectation'] ?>" readonly/>
            </div>
            <div class="field">
              <label>Fonction occupée</label>
              <input type="text" id="inp-fonction" name="fonction_collab" value="<?= $entretien['fonction'] ?>" readonly/>
            </div>
            <div class="field">
              <label>Ancienneté dans la fonction</label>
              <input type="text" id="inp-anciennete" name="anciennete_fonction" value="<?= $entretien['anciennete'] ?> ans" readonly/>
            </div>
          </div>
          <div class="divider"></div>
          <div class="grid-3">
            <div class="field">
              <label>Manager évaluateur (N+1)</label>
              <input type="text" name="manager_nom" value="<?= $entretien['nom_manager'] ?>" readonly/>
            </div>
            <div class="field">
              <label>Fonction du manager</label>
              <input type="text" name="manager_fonction" value="<?= $entretien['fonction_manager'] ?>" readonly/>
            </div>
            <div class="field">
              <label>Date de l'entretien</label>
              <input type="date" name="date_entretien" value="<?= $entretien['date_entretien'] ?>" readonly/>
            </div>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════
       SECTION 1 — RESPONSABILITÉS & TENUE DU POSTE
  ═══════════════════════════════════════════ -->
      <div class="section">
        <div class="section-header">
          <div class="section-number">1</div>
          <div>
            <div class="section-title">Responsabilités professionnelles — Bilan <span id="annee-s1">2025</span></div>
            <div class="section-sub">Évaluation de la tenue du poste au regard des missions permanentes</div>
          </div>
        </div>
        <div class="section-body">

          <!-- Missions -->
          <div class="field collab">
            <label>Missions principales du poste 
            <textarea name="missions_principales" readonly><?= $entretien['missions_fonction'] ?></textarea>
          </div>
          <div class="field collab">
            <label>Missions ponctuelles / transversales <span class="role-tag collab">Collaborateur</span></label>
            <textarea name="missions_ponctuelles"
              style="min-height:70px;" readonly><?= $entretien['mission_ponctuelles'] ?></textarea>
          </div>

          <!-- Niveau d'expertise -->
          <div>
            <div
              style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">
              Niveau dans la tenue de la fonction <span class="role-tag manager">Manager</span>
            </div>
            <div class="radio-group">
              <div class="radio-option">
                <input type="radio" name="niveau_expertise" id="niv_debutant" value="Débutant" <?= $entretien['niveau'] == 1 ? 'checked' : '' ?>>
                <label for="niv_debutant">🌱 Débutant</label>
              </div>
              <div class="radio-option">
                <input type="radio" name="niveau_expertise" id="niv_intermediaire" value="Intermédiaire" <?= $entretien['niveau'] == 2 ? 'checked' : '' ?>>
                <label for="niv_intermediaire">📈 Intermédiaire</label>
              </div>
              <div class="radio-option">
                <input type="radio" name="niveau_expertise" id="niv_confirme" value="Confirmé" <?= $entretien['niveau'] == 3 ? 'checked' : '' ?>>
                <label for="niv_confirme">✅ Confirmé</label>
              </div>
              <div class="radio-option">
                <input type="radio" name="niveau_expertise" id="niv_senior" value="Senior" <?= $entretien['niveau'] == 4 ? 'checked' : '' ?>>
                <label for="niv_senior">⭐ Senior</label>
              </div>
            </div>
          </div>

          <!-- Commentaires -->
          <div class="dual-comment">
            <div>
              <div class="col-head manager">Commentaires du manager</div>
              <textarea name="bilan_manager_s1"
                placeholder="Pas de commentaire"
                style="min-height:120px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:10px 12px;font-family:inherit;font-size:13px;width:100%;resize:vertical;color:var(--text);"
                readonly><?= $entretien['commentaire_bilan'] ?></textarea>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════
       SECTION 2 — PERFORMANCE DU COLLABORATEUR
  ═══════════════════════════════════════════ -->
      <div class="section">
        <div class="section-header">
          <div class="section-number">2</div>
          <div>
            <div class="section-title">Performance du collaborateur — <span id="annee-s2">2025</span></div>
            <div class="section-sub">Évaluation des compétences clés par le manager</div>
          </div>
        </div>
        <div class="section-body">

          <table class="perf-table">
            <thead>
              <tr>
                <th style="width:35%">Compétence / Critère</th>
                <th style="width:20%">Note <span style="color:rgba(255,255,255,0.4);font-weight:400;">(1 à 5)</span>
                </th>
                <th>Commentaire du manager <span class="role-tag manager" style="font-size:9px;">Manager</span></th>
              </tr>
            </thead>
            <tbody id="perf-rows">
              <!-- Row template -->
              <tr>
                <td style="font-weight:500;">Maîtrise technique du poste</td>
                <td>
                  <div class="score-input">
                    <input type="number" name="perf_1_note" value="<?= $notes['1']['note'] ?? '0' ?>" readonly>
                    <span class="unit">/5</span>
                  </div>
                  <div class="score-bar-wrap" style="margin-top:6px;">
                    <div class="score-bar">
                      <div class="score-bar-fill" id="bar_1" style="width:0%"></div>
                    </div>
                  </div>
                </td>
                <td><textarea name="perf_1_comment"
                    style="min-height:60px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:12.5px;width:100%;resize:vertical;color:var(--text);"
                    readonly><?= $notes['1']['commentaire'] ?? 'Pas de commentaire' ?></textarea></td>
              </tr>
              <tr>
                <td style="font-weight:500;">Organisation & gestion des priorités</td>
                <td>
                  <div class="score-input">
                    <input type="number" name="perf_2_note" value="<?= $notes['2']['note'] ?? '0' ?>" readonly>
                    <span class="unit">/5</span>
                  </div>
                  <div class="score-bar-wrap" style="margin-top:6px;">
                    <div class="score-bar">
                      <div class="score-bar-fill" id="bar_2" style="width:0%"></div>
                    </div>
                  </div>
                </td>
                <td><textarea name="perf_2_comment"
                    style="min-height:60px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:12.5px;width:100%;resize:vertical;color:var(--text);"
                    readonly><?= $notes['2']['commentaire'] ?? 'Pas de commentaire' ?></textarea></td>
              </tr>
              <tr>
                <td style="font-weight:500;">Qualité du travail & fiabilité</td>
                <td>
                  <div class="score-input">
                    <input type="number" name="perf_3_note" value="<?= $notes['3']['note'] ?? '0' ?>" readonly>
                    <span class="unit">/5</span>
                  </div>
                  <div class="score-bar-wrap" style="margin-top:6px;">
                    <div class="score-bar">
                      <div class="score-bar-fill" id="bar_3" style="width:0%"></div>
                    </div>
                  </div>
                </td>
                <td><textarea name="perf_3_comment"
                    style="min-height:60px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:12.5px;width:100%;resize:vertical;color:var(--text);"
                    readonly><?= $notes['3']['commentaire'] ?? 'Pas de commentaire' ?></textarea></td>
              </tr>
              <tr>
                <td style="font-weight:500;">Esprit d'équipe & coopération</td>
                <td>
                  <div class="score-input">
                    <input type="number" name="perf_4_note" value="<?= $notes['4']['note'] ?? '0' ?>" readonly>
                    <span class="unit">/5</span>
                  </div>
                  <div class="score-bar-wrap" style="margin-top:6px;">
                    <div class="score-bar">
                      <div class="score-bar-fill" id="bar_4" style="width:0%"></div>
                    </div>
                  </div>
                </td>
                <td><textarea name="perf_4_comment"
                    style="min-height:60px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:12.5px;width:100%;resize:vertical;color:var(--text);"
                    readonly><?= $notes['4']['commentaire'] ?? 'Pas de commentaire' ?></textarea></td>
              </tr>
              <tr>
                <td style="font-weight:500;">Communication & respect des valeurs</td>
                <td>
                  <div class="score-input">
                    <input type="number" name="perf_5_note" value="<?= $notes['5']['note'] ?? '0' ?>" readonly>
                    <span class="unit">/5</span>
                  </div>
                  <div class="score-bar-wrap" style="margin-top:6px;">
                    <div class="score-bar">
                      <div class="score-bar-fill" id="bar_5" style="width:0%"></div>
                    </div>
                  </div>
                </td>
                <td><textarea name="perf_5_comment"
                    style="min-height:60px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:12.5px;width:100%;resize:vertical;color:var(--text);"
                    readonly><?= $notes['5']['commentaire'] ?? 'Pas de commentaire' ?></textarea></td>
              </tr>
              <tr>
                <td style="font-weight:500;">Initiative & force de proposition</td>
                <td>
                  <div class="score-input">
                    <input type="number" name="perf_6_note" value="<?= $notes['6']['note'] ?? '0' ?>" readonly>
                    <span class="unit">/5</span>
                  </div>
                  <div class="score-bar-wrap" style="margin-top:6px;">
                    <div class="score-bar">
                      <div class="score-bar-fill" id="bar_6" style="width:0%"></div>
                    </div>
                  </div>
                </td>
                <td><textarea name="perf_6_comment"
                    style="min-height:60px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:12.5px;width:100%;resize:vertical;color:var(--text);"
                    readonly><?= $notes['6']['commentaire'] ?? 'Pas de commentaire' ?></textarea></td>
              </tr>
              <tr class="total-row">
                <td>Note moyenne</td>
                <td colspan="2">
                  <span style="font-family:'Cormorant Garamond',serif;font-size:22px;color:var(--gold);"
                    id="note-moy">—</span>
                  <span style="font-size:13px;color:var(--muted);margin-left:4px;">/5</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════
       SECTION 3 — AXES DE PROGRÈS
  ═══════════════════════════════════════════ -->
      <div class="section">
        <div class="section-header">
          <div class="section-number">3</div>
          <div>
            <div class="section-title">Axes de progrès pour l'année à venir</div>
            <div class="section-sub">Pistes d'amélioration définies par le manager</div>
          </div>
        </div>
        <div class="section-body">

          <div>
            <div class="axe-header">
              <div class="axe-col-head">Axe de progrès <span class="role-tag manager">Manager</span></div>
              <div class="axe-col-head">Moyens / Actions à mettre en œuvre <span class="role-tag manager">Manager</span>
              </div>
             
             
            </div>

            <div id="axes-container">
              <?php foreach ($axes as $axe) { ?>
                <div class="axe-row">
                  <div><textarea name="axe_<?= $axe['id'] ?>"
                      style="min-height:80px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:13px;width:100%;resize:vertical;color:var(--text);"
                      readonly><?= $axe['label'] ?></textarea>
                  </div>
                  <div><textarea name="axe_<?= $axe['id'] ?>_moyen"
                      style="min-height:80px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:13px;width:100%;resize:vertical;color:var(--text);"
                      readonly><?= $axe['description'] ?></textarea>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════
       SECTION 4 — SYNTHÈSE DE L'ENTRETIEN
  ═══════════════════════════════════════════ -->
      <div class="section">
        <div class="section-header">
          <div class="section-number">4</div>
          <div>
            <div class="section-title">Questionnaire 360° — QCM (format sécurisé, anonyme, sans risque de conflit)</div>
            <div class="section-sub">Évaluation du Manager — répondu par les collaborateurs (subordonnés / pairs)</div>
          </div>
        </div>
        <div class="section-body">

          <!-- Appréciations -->
          <div class="qcm-section">
            <div
              style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:12px;">
              Évaluation du management <span class="role-tag collab">Collaborateur</span>
            </div>

            <div class="qcm-list">

              <!-- Q1 -->
              <div class="qcm-block">
                <div class="qcm-question">1. Comment décririez-vous le style de management de votre responsable ?</div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_style_management" id="qcm_style_management_a"
                      value="Directif et clair dans ses attentes" data-score="4" <?= $reponses['1'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_style_management_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Directif et clair dans ses attentes</span>
                      <span class="qcm-desc">Management structuré et cadré</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_style_management" id="qcm_style_management_b"
                      value="Participatif et à l'écoute de l'équipe" data-score="3" <?= $reponses['1'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_style_management_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Participatif et à l'écoute de l'équipe</span>
                      <span class="qcm-desc">Management ouvert et collaboratif</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_style_management" id="qcm_style_management_c"
                      value="Peu présent ou distant au quotidien" data-score="2" <?= $reponses['1'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_style_management_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Peu présent ou distant au quotidien</span>
                      <span class="qcm-desc">Accompagnement limité</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_style_management" id="qcm_style_management_d"
                      value="Incohérent ou imprévisible" data-score="1" <?= $reponses['1'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_style_management_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Incohérent ou imprévisible</span>
                      <span class="qcm-desc">Management instable</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Q2 -->
              <div class="qcm-block">
                <div class="qcm-question">2. Votre manager vous aide-t-il/elle à atteindre vos objectifs ?</div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_aide_objectifs" id="qcm_aide_objectifs_a"
                      value="Toujours — il/elle me guide activement" data-score="4" <?= $reponses['2'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_aide_objectifs_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Toujours</span>
                      <span class="qcm-desc">Il/elle me guide activement</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_aide_objectifs" id="qcm_aide_objectifs_b"
                      value="Souvent — quand je lui demande" data-score="3" <?= $reponses['2'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_aide_objectifs_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Souvent</span>
                      <span class="qcm-desc">Quand je lui demande</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_aide_objectifs" id="qcm_aide_objectifs_c"
                      value="Rarement — je me débrouille seul(e)" data-score="2" <?= $reponses['2'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_aide_objectifs_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Rarement</span>
                      <span class="qcm-desc">Je me débrouille seul(e)</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_aide_objectifs" id="qcm_aide_objectifs_d"
                      value="Jamais — aucun soutien" data-score="1" <?= $reponses['2'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_aide_objectifs_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Jamais</span>
                      <span class="qcm-desc">Aucun soutien</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Q3 -->
              <div class="qcm-block">
                <div class="qcm-question">3. Les consignes et priorités données par votre manager sont-elles claires ?
                </div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_clarte_consigne" id="qcm_clarte_consigne_a"
                      value="Toujours très claires" data-score="4" <?= $reponses['3'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_clarte_consigne_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Toujours très claires</span>
                      <span class="qcm-desc">Priorités bien définies</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_clarte_consigne" id="qcm_clarte_consigne_b"
                      value="Généralement claires" data-score="3" <?= $reponses['3'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_clarte_consigne_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Généralement claires</span>
                      <span class="qcm-desc">Quelques ajustements parfois nécessaires</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_clarte_consigne" id="qcm_clarte_consigne_c" 
                    value="Parfois confuses" data-score="2" <?= $reponses['3'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_clarte_consigne_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Parfois confuses</span>
                      <span class="qcm-desc">Manque de lisibilité ponctuel</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_clarte_consigne" id="qcm_clarte_consigne_d"
                      value="Souvent floues ou contradictoires" data-score="1" <?= $reponses['3'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_clarte_consigne_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Souvent floues ou contradictoires</span>
                      <span class="qcm-desc">Consignes instables ou ambiguës</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Q4 -->
              <div class="qcm-block">
                <div class="qcm-question">4. Votre manager reconnaît-il/elle vos efforts et vos réussites ?</div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_reconnaissance" id="qcm_reconnaissance_a"
                      value="Oui, régulièrement et sincèrement" data-score="4" <?= $reponses['4'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_reconnaissance_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Oui, régulièrement et sincèrement</span>
                      <span class="qcm-desc">Reconnaissance forte</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_reconnaissance" id="qcm_reconnaissance_b"
                      value="Oui, de temps en temps" data-score="3" <?= $reponses['4'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_reconnaissance_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Oui, de temps en temps</span>
                      <span class="qcm-desc">Reconnaissance occasionnelle</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_reconnaissance" id="qcm_reconnaissance_c" value="Rarement"
                      data-score="2" <?= $reponses['4'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_reconnaissance_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Rarement</span>
                      <span class="qcm-desc">Valorisation peu visible</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_reconnaissance" id="qcm_reconnaissance_d" value="Non, jamais"
                      data-score="1" <?= $reponses['4'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_reconnaissance_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Non, jamais</span>
                      <span class="qcm-desc">Aucune reconnaissance perçue</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Q5 -->
              <div class="qcm-block">
                <div class="qcm-question">5. Comment évaluez-vous la communication de votre manager avec l'équipe ?
                </div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_communication" id="qcm_communication_a"
                      value="Excellente — transparente et régulière" data-score="4" <?= $reponses['5'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_communication_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Excellente</span>
                      <span class="qcm-desc">Transparente et régulière</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_communication" id="qcm_communication_b"
                      value="Bonne — les infos essentielles circulent" data-score="3" <?= $reponses['5'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_communication_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Bonne</span>
                      <span class="qcm-desc">Les infos essentielles circulent</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_communication" id="qcm_communication_c"
                      value="Insuffisante — on manque souvent d'infos" data-score="2" <?= $reponses['5'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_communication_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Insuffisante</span>
                      <span class="qcm-desc">On manque souvent d'informations</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_communication" id="qcm_communication_d"
                      value="Mauvaise — communication quasi absente" data-score="1" <?= $reponses['5'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_communication_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Mauvaise</span>
                      <span class="qcm-desc">Communication quasi absente</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Q6 -->
              <div class="qcm-block">
                <div class="qcm-question">6. En cas de difficulté ou tension dans l'équipe, votre manager
                  intervient-il/elle de façon appropriée ?</div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_gestion_tension" id="qcm_gestion_tension_a"
                      value="Oui, toujours avec équité et rapidité" data-score="4" <?= $reponses['6'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_gestion_tension_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Oui, toujours</span>
                      <span class="qcm-desc">Avec équité et rapidité</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_gestion_tension" id="qcm_gestion_tension_b"
                      value="Souvent, mais pas systématiquement" data-score="3" <?= $reponses['6'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_gestion_tension_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Souvent</span>
                      <span class="qcm-desc">Mais pas systématiquement</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_gestion_tension" id="qcm_gestion_tension_c"
                      value="Rarement — les problèmes traînent" data-score="2" <?= $reponses['6'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_gestion_tension_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Rarement</span>
                      <span class="qcm-desc">Les problèmes traînent</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_gestion_tension" id="qcm_gestion_tension_d"
                      value="Non — les situations difficiles ne sont pas gérées" data-score="1" <?= $reponses['6'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_gestion_tension_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Non</span>
                      <span class="qcm-desc">Les situations difficiles ne sont pas gérées</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Q7 -->
              <div class="qcm-block">
                <div class="qcm-question">7. Votre manager favorise-t-il/elle votre développement professionnel
                  (formation, feedback, responsabilités) ?</div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_developpement" id="qcm_developpement_a"
                      value="Oui, activement et régulièrement" data-score="4" <?= $reponses['7'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_developpement_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Oui, activement et régulièrement</span>
                      <span class="qcm-desc">Développement encouragé</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_developpement" id="qcm_developpement_b"
                      value="Parfois — selon les ressources disponibles" data-score="3" <?= $reponses['7'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_developpement_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Parfois</span>
                      <span class="qcm-desc">Selon les ressources disponibles</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_developpement" id="qcm_developpement_c"
                      value="Peu — ce n'est pas sa priorité visible" data-score="2" <?= $reponses['7'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_developpement_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Peu</span>
                      <span class="qcm-desc">Ce n'est pas sa priorité visible</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_developpement" id="qcm_developpement_d"
                      value="Non — aucun effort dans ce sens" data-score="1" <?= $reponses['7'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_developpement_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Non</span>
                      <span class="qcm-desc">Aucun effort dans ce sens</span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Q8 -->
              <div class="qcm-block">
                <div class="qcm-question">8. Dans l'ensemble, comment évaluez-vous votre manager ?</div>
                <div class="qcm-options">
                  <div class="appr-card">
                    <input type="radio" name="qcm_eval_globale_manager" id="qcm_eval_globale_manager_a"
                      value="Excellent(e) manager" data-score="4" <?= $reponses['8'] == 4 ? 'checked' : '' ?> disabled>
                    <label for="qcm_eval_globale_manager_a">
                      <span class="qcm-badge a">A</span>
                      <span class="qcm-title">Excellent(e) manager</span>
                      <span class="qcm-desc">Très forte satisfaction</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_eval_globale_manager" id="qcm_eval_globale_manager_b"
                      value="Bon(ne) manager — quelques axes à améliorer" data-score="3" <?= $reponses['8'] == 3 ? 'checked' : '' ?> disabled>
                    <label for="qcm_eval_globale_manager_b">
                      <span class="qcm-badge b">B</span>
                      <span class="qcm-title">Bon(ne) manager</span>
                      <span class="qcm-desc">Quelques axes à améliorer</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_eval_globale_manager" id="qcm_eval_globale_manager_c"
                      value="Manager moyen(ne) — des progrès importants à faire" data-score="2" <?= $reponses['8'] == 2 ? 'checked' : '' ?> disabled>
                    <label for="qcm_eval_globale_manager_c">
                      <span class="qcm-badge c">C</span>
                      <span class="qcm-title">Manager moyen(ne)</span>
                      <span class="qcm-desc">Des progrès importants à faire</span>
                    </label>
                  </div>
                  <div class="appr-card">
                    <input type="radio" name="qcm_eval_globale_manager" id="qcm_eval_globale_manager_d"
                      value="Management à revoir profondément" data-score="1" <?= $reponses['8'] == 1 ? 'checked' : '' ?> disabled>
                    <label for="qcm_eval_globale_manager_d">
                      <span class="qcm-badge d">D</span>
                      <span class="qcm-title">Management à revoir profondément</span>
                      <span class="qcm-desc">Refonte nécessaire</span>
                    </label>
                  </div>
                </div>
              </div>

            </div>
          </div>






        </div>
      </div>


      <!-- ═══════════════════════════════════════════
       SECTION 5 — PLAN DE FORMATION
  ═══════════════════════════════════════════ -->
      <div class="section">
        <div class="section-header">
          <div class="section-number">5</div>
          <div>
            <div class="section-title">Plan de formation individuel</div>
            <div class="section-sub">Sous réserve de validation par la Direction Générale et le budget</div>
          </div>
        </div>
        <div class="section-body">
          <div
            style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;padding-bottom:10px;border-bottom:1.5px solid var(--border);">
            <div
              style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);">
              Formation souhaitée</div>
            <div
              style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);">
              Priorité</div>
            <div
              style="font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);">
              Demandeur</div>
          </div>

          <div id="formations-container" class="formations-list">

            <?php for($i = 0; $i < count($formations); $i++) { $formation = $formations[$i]; ?>
            <div class="formation-item">
              <div class="formation-head">
                <span class="formation-badge">Formation <?= $i + 1 ?></span>
              </div>

              <div class="formation-grid">
                <div class="field-group field-wide">
                  <label for="form_1_titre">Intitulé</label>
                  <input id="form_1_titre" type="text" name="form_1_titre"
                    value="<?= $formation['titre'] ?>" readonly/>
                </div>

                <div class="field-group">
                  <label for="form_1_priorite">Priorité</label>
                  <input id="form_1_priorite" type="text" name="form_1_priorite"
                    value="<?= $priorites[$formation['priorite']] ?>" readonly/>
                </div>

                <div class="field-group">
                  <label for="form_1_demandeur">Demandeur</label>
                  <input id="form_1_demandeur" type="text" name="form_1_demandeur"
                    value="<?= $demandeurs[$formation['demandeur']] ?>" readonly/>
                </div>
              </div>
            </div>
            <?php } ?>

          </div>

          <div class="field manager" style="margin-top:8px;">
            <label>Commentaires du manager sur le plan de formation <span
                class="role-tag manager">Manager</span></label>
            <textarea name="formation_commentaire"
              style="min-height:70px;" readonly><?= $entretien['commentaire_formation'] ?></textarea>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════
       SECTION 6 — SIGNATURES & ATTESTATION
  ═══════════════════════════════════════════ -->
      <div class="section">
        <div class="section-header">
          <div class="section-number">6</div>
          <div>
            <div class="section-title">Signatures & Attestation</div>
            <div class="section-sub">Validation de l'entretien par les deux parties</div>
          </div>
        </div>
        <div class="section-body">
          <div class="notice">
            <span class="notice-icon">📄</span>
            En signant ce document, le collaborateur et le manager attestent que l'entretien a bien eu lieu. La
            signature ne constitue pas nécessairement un accord sur le contenu. Une copie est remise au collaborateur et
            l'original transmis à la DRH.
          </div>

          <div class="signature-section">
            <div class="sig-box collab">
              <h4>Collaborateur</h4>
              <div class="field">
                <label>Nom & Prénom</label>
                <input type="text" name="sig_collab_nom" id="sig_collab_auto" value="<?= $entretien['nom_complet'] ?>" readonly />
              </div>
              <div class="field">
                <label>Date de signature</label>
                <input type="date" name="sig_collab_date" value="<?= $entretien['date_signature_colab'] ?>" readonly/>
              </div>

            </div>

            <div class="sig-box manager">
              <h4>Manager</h4>
              <div class="field">
                <label>Nom & Prénom</label>
                <input type="text" name="sig_manager_nom" id="sig_manager_auto" value="<?= $entretien['nom_manager'] ?>" readonly />
              </div>
              <div class="field">
                <label>Date de signature</label>
                <input type="date" name="sig_manager_date" value="<?= $entretien['date_signature_manager'] ?>" readonly/>
              </div>

            </div>
          </div>

          <div class="field collab" style="margin-top:8px;">
            <label>Commentaire libre du collaborateur (optionnel) <span
                class="role-tag collab">Collaborateur</span></label>
            <textarea name="commentaire_final_collab"
              placeholder="Pas de commentaire"
              style="min-height:70px;" readonly><?= $entretien['commentaire_libre'] ?></textarea>
          </div>
        </div>
      </div>

    </div><!-- /form-body -->

    <!-- FOOTER ACTIONS -->
    <div class="form-footer">
      <button type="button" class="btn btn-secondary" onclick="window.print()">🖨 Imprimer</button>
    </div>
  </form>

</body>

</html>