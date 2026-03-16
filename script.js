
let axeCount = 0;
let formationCount = 0;

document.addEventListener('DOMContentLoaded', function () {
  initDatalist();
  initCounts();
  initBindings();
  updateEvaluationYear();
  computeAverage();
  syncSignatureNames();
});


function initCounts() {
  axeCount = document.querySelectorAll('#axes-container .axe-row').length || 0;
  formationCount = document.querySelectorAll('#formations-container .formation-item').length || 0;
}

function initBindings() {
  const collabInput = document.getElementById('inp-collab-nom');
  const managerInput = document.querySelector('[name="manager_nom"]');
  const dateEntretienInput = document.querySelector('[name="date_entretien"]');
  const form = document.getElementById('eaa-form');
  const addFormationBtn = document.getElementById('add-formation-btn');

  if (collabInput) {
    collabInput.addEventListener('input', syncSignatureNames);
  }

  if (managerInput) {
    managerInput.addEventListener('input', syncSignatureNames);
  }

  if (dateEntretienInput) {
    dateEntretienInput.addEventListener('change', updateEvaluationYear);
  }

  document.querySelectorAll('[name="niveau_expertise"]').forEach(radio => {
    radio.addEventListener('change', function () {
      setTextIfExists('summary-niveau', this.value || '—');
    });
  });

  if (addFormationBtn) {
    addFormationBtn.addEventListener('click', addFormation);
  }

  if (form) {
    form.addEventListener('submit', handleSubmit);
  }

  for (let i = 1; i <= 6; i++) {
    const input = document.querySelector(`[name="perf_${i}_note"]`);
    if (input) {
      input.addEventListener('input', function () {
        updateBar(this, `bar_${i}`);
      });
    }
  }
}

/* =========================================================
   HELPERS
========================================================= */

function setTextIfExists(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value;
}

function setField(id, value) {
  const el = document.getElementById(id);
  if (el) el.value = value ?? '';
}

function getValueByName(name) {
  return document.querySelector(`[name="${name}"]`)?.value?.trim() || '';
}

function getCheckedValue(name) {
  return document.querySelector(`[name="${name}"]:checked`)?.value || '';
}

function getCheckedDataScore(name) {
  const el = document.querySelector(`[name="${name}"]:checked`);
  return el ? parseInt(el.dataset.score || '0', 10) : null;
}

function syncSignatureNames() {
  const collabName = document.getElementById('inp-collab-nom')?.value || '';
  const managerName = document.querySelector('[name="manager_nom"]')?.value || '';

  const sigCollabAuto = document.getElementById('sig_collab_auto');
  const sigCollabName = document.querySelector('[name="sig_collab_nom"]');
  const sigManagerAuto = document.getElementById('sig_manager_auto');
  const sigManagerName = document.querySelector('[name="sig_manager_nom"]');

  if (sigCollabAuto) sigCollabAuto.value = collabName;
  if (sigCollabName) sigCollabName.value = collabName;

  if (sigManagerAuto) sigManagerAuto.value = managerName;
  if (sigManagerName) sigManagerName.value = managerName;
}

function updateEvaluationYear() {
  const dateEntretien = document.querySelector('[name="date_entretien"]')?.value;
  const now = new Date();

  let year = now.getFullYear() - 1;

  if (dateEntretien) {
    const selectedYear = new Date(dateEntretien).getFullYear();
    if (!isNaN(selectedYear)) {
      year = selectedYear - 1;
    }
  }

  setTextIfExists('annee-label', String(year));
  setTextIfExists('annee-s1', String(year));
  setTextIfExists('annee-s2', String(year));
}

/* =========================================================
   DATALIST
========================================================= */

function initDatalist() {
  const dl = document.getElementById('mat-list');
  if (!dl) return;

  dl.innerHTML = '';

  if (typeof TOUS_MATRICULES !== 'undefined' && Array.isArray(TOUS_MATRICULES)) {
    TOUS_MATRICULES.forEach(matricule => {
      const opt = document.createElement('option');
      opt.value = matricule;

      if (typeof EMPLOYES !== 'undefined' && EMPLOYES[matricule]) {
        const emp = EMPLOYES[matricule];
        opt.label = `${emp.nom || ''} ${emp.prenoms || ''}`.trim();
      }

      dl.appendChild(opt);
    });
  }
}

/* =========================================================
   AUTO-FILL EMPLOYÉ DEPUIS BDD
========================================================= */

window.autoFillFromMatricule = async function (val) {
  const mat = val.trim();
  const badge = document.getElementById('lookup-badge');

  if (!mat) {
    clearEmployeeFields();
    if (badge) badge.style.display = 'none';
    return;
  }

  try {
    const response = await fetch(`api/employe_get.php?matricule=${encodeURIComponent(mat)}`);
    const json = await response.json();

    if (!json.success) {
      clearEmployeeFields();

      if (badge) {
        badge.textContent = '✗ Matricule introuvable dans la base';
        badge.style.display = 'flex';
        badge.className = 'lookup-badge notfound';
      }
      return;
    }

    const emp = json.data || {};

    setField('inp-employe-id', emp.id || '');
    setField('inp-fonction-id', emp.id_fonction || '');
    setField('inp-manager-id', emp.manager_id || '');

    setField('inp-collab-nom', emp.nom_complet || '');
    setField('inp-date-embauche', emp.date_embauche || '');
    setField('inp-lieu', emp.lieu || '');
    setField('inp-fonction', emp.fonction || '');
    setField('inp-anciennete', emp.anciennete || '');

    const managerNomInput = document.querySelector('[name="manager_nom"]');
    const managerFonctionInput = document.querySelector('[name="manager_fonction"]');

    if (managerNomInput) managerNomInput.value = emp.manager_nom_complet || '';
    if (managerFonctionInput) managerFonctionInput.value = emp.manager_fonction || '';

    const missionsEl = document.querySelector('[name="missions_principales"]');
    const localMissions =
      typeof MISSIONS_PAR_POSTE !== 'undefined' && emp.fonction
        ? (MISSIONS_PAR_POSTE[emp.fonction] || '')
        : '';

    if (missionsEl && !missionsEl.value) {
      missionsEl.value = emp.missions || localMissions || '';
    }

    syncSignatureNames();

    if (badge) {
      badge.textContent = `✓ ${emp.nom_complet || ''} — ${emp.fonction || ''} (${emp.lieu || ''})`;
      badge.style.display = 'flex';
      badge.className = 'lookup-badge found';
    }
  } catch (error) {
    console.error(error);

    if (badge) {
      badge.textContent = '✗ Erreur serveur lors du chargement';
      badge.style.display = 'flex';
      badge.className = 'lookup-badge notfound';
    }
  }
};

function clearEmployeeFields() {
  [
    'inp-employe-id',
    'inp-fonction-id',
    'inp-manager-id',
    'inp-collab-nom',
    'inp-date-embauche',
    'inp-lieu',
    'inp-fonction',
    'inp-anciennete'
  ].forEach(id => setField(id, ''));

  const managerNomInput = document.querySelector('[name="manager_nom"]');
  const managerFonctionInput = document.querySelector('[name="manager_fonction"]');
  const missionsEl = document.querySelector('[name="missions_principales"]');

  if (managerNomInput) managerNomInput.value = '';
  if (managerFonctionInput) managerFonctionInput.value = '';
  if (missionsEl) missionsEl.value = '';

  syncSignatureNames();
}

/* =========================================================
   PERFORMANCE
========================================================= */

window.updateBar = function (input, barId) {
  let val = parseFloat(input.value);

  if (isNaN(val)) {
    val = 0;
  } else {
    val = Math.min(Math.max(val, 1), 5);
    input.value = val;
  }

  const pct = Math.min(Math.max((val / 5) * 100, 0), 100);
  const bar = document.getElementById(barId);

  if (bar) {
    bar.style.width = pct + '%';
  }

  computeAverage();
};

function computeAverage() {
  const notes = [];

  for (let i = 1; i <= 6; i++) {
    const inp = document.querySelector(`[name="perf_${i}_note"]`);
    const v = parseFloat(inp?.value);

    if (!isNaN(v)) {
      notes.push(Math.min(5, Math.max(1, v)));
    }
  }

  const avg = notes.length
    ? (notes.reduce((a, b) => a + b, 0) / notes.length).toFixed(2)
    : '—';

  setTextIfExists('note-moy', avg);
  setTextIfExists('summary-note', avg);
}

/* =========================================================
   AXES DE PROGRÈS
   NB: 2 champs uniquement pour correspondre à la BDD
   - label
   - description
========================================================= */

window.addAxe = function () {
  axeCount++;
  const container = document.getElementById('axes-container');
  if (!container) return;

  const div = document.createElement('div');
  div.className = 'axe-row';
  div.innerHTML = `
    <div>
      <textarea
        name="axe_${axeCount}"
        placeholder="Axe de progrès n°${axeCount}…"
        style="min-height:80px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:13px;width:100%;resize:vertical;color:var(--text);"></textarea>
    </div>
    <div>
      <textarea
        name="axe_${axeCount}_moyen"
        placeholder="Moyens envisagés…"
        style="min-height:80px;border:1.5px solid var(--manager-border);background:var(--manager-bg);border-radius:8px;padding:8px 10px;font-family:inherit;font-size:13px;width:100%;resize:vertical;color:var(--text);"></textarea>
    </div>
  `;

  container.appendChild(div);
};

/* =========================================================
   FORMATIONS
========================================================= */

function addFormation() {
  formationCount++;
  const container = document.getElementById('formations-container');
  if (!container) return;

  const div = document.createElement('div');
  div.className = 'formation-item';

  div.innerHTML = `
    <div class="formation-head">
      <span class="formation-badge">Formation ${formationCount}</span>
    </div>

    <div class="formation-grid">
      <div class="field-group field-wide">
        <label for="form_${formationCount}_titre">Intitulé</label>
        <input
          id="form_${formationCount}_titre"
          type="text"
          name="form_${formationCount}_titre"
          placeholder="Ex. Excel avancé, management d'équipe, sécurité…" />
      </div>

      <div class="field-group">
        <label for="form_${formationCount}_priorite">Priorité</label>
        <select id="form_${formationCount}_priorite" name="form_${formationCount}_priorite">
          <option value="">Sélectionner</option>
          <option value="1 - Urgente">1 - Urgente</option>
          <option value="2 - Importante">2 - Importante</option>
          <option value="3 - Souhaitable">3 - Souhaitable</option>
        </select>
      </div>

      <div class="field-group">
        <label for="form_${formationCount}_demandeur">Demandeur</label>
        <select id="form_${formationCount}_demandeur" name="form_${formationCount}_demandeur">
          <option value="">Sélectionner</option>
          <option value="Collaborateur">Collaborateur</option>
          <option value="Manager">Manager</option>
          <option value="Les deux">Les deux</option>
        </select>
      </div>
    </div>
  `;

  container.appendChild(div);
}

/* =========================================================
   RESET
========================================================= */

window.resetForm = function () {
  if (!confirm('Êtes-vous sûr de vouloir réinitialiser tous les champs ?')) {
    return;
  }

  const form = document.getElementById('eaa-form');
  if (form) form.reset();

  setTextIfExists('note-moy', '—');
  setTextIfExists('summary-note', '—');
  setTextIfExists('summary-niveau', '—');
  setTextIfExists('summary-appr', '—');

  for (let i = 1; i <= 6; i++) {
    const b = document.getElementById('bar_' + i);
    if (b) b.style.width = '0%';
  }

  const badge = document.getElementById('lookup-badge');
  if (badge) badge.style.display = 'none';

  clearEmployeeFields();
  updateEvaluationYear();

  // Supprimer les axes ajoutés dynamiquement en gardant les 2 premiers
  const axeRows = document.querySelectorAll('#axes-container .axe-row');
  axeRows.forEach((row, index) => {
    if (index >= 2) row.remove();
  });

  // Supprimer les formations ajoutées dynamiquement en gardant les 2 premières
  const formations = document.querySelectorAll('#formations-container .formation-item');
  formations.forEach((item, index) => {
    if (index >= 2) item.remove();
  });

  initCounts();
};

/* =========================================================
   PAYLOAD
========================================================= */

function buildEntretienPayload() {
  const niveauMap = {
    'Débutant': 1,
    'Intermédiaire': 2,
    'Confirmé': 3,
    'Senior': 4
  };

  const performance = [];
  for (let i = 1; i <= 6; i++) {
    performance.push({
      num_question: i,
      note: parseInt(getValueByName(`perf_${i}_note`) || '0', 10) || null,
      commentaire: getValueByName(`perf_${i}_comment`)
    });
  }

  const qcmFields = [
    'qcm_style_management',
    'qcm_aide_objectifs',
    'qcm_clarte_consigne',
    'qcm_reconnaissance',
    'qcm_communication',
    'qcm_gestion_tension',
    'qcm_developpement',
    'qcm_eval_globale_manager'
  ];

  const qcm = qcmFields.map((field, index) => ({
    num_question: index + 1,
    reponse: getCheckedDataScore(field)
  }));

  const axes = [];
  document.querySelectorAll('#axes-container .axe-row').forEach((row) => {
    const textareas = row.querySelectorAll('textarea');
    const label = textareas[0]?.value?.trim() || '';
    const description = textareas[1]?.value?.trim() || '';

    if (label || description) {
      axes.push({ label, description });
    }
  });

  const formations = [];
  document.querySelectorAll('#formations-container .formation-item').forEach((item) => {
    const titre = item.querySelector('input[name$="_titre"]')?.value?.trim() || '';
    const prioriteText = item.querySelector('select[name$="_priorite"]')?.value || '';
    const demandeurText = item.querySelector('select[name$="_demandeur"]')?.value || '';

    const prioriteMap = {
      '1 - Urgente': 1,
      '2 - Importante': 2,
      '3 - Souhaitable': 3
    };

    const demandeurMap = {
      'Collaborateur': 1,
      'Manager': 2,
      'Les deux': 3
    };

    if (titre) {
      formations.push({
        titre,
        priorite: prioriteMap[prioriteText] || null,
        demandeur: demandeurMap[demandeurText] || null
      });
    }
  });

  return {
    id_employe: parseInt(document.getElementById('inp-employe-id')?.value || '0', 10) || null,
    id_fonction: parseInt(document.getElementById('inp-fonction-id')?.value || '0', 10) || null,
    id_manager: document.getElementById('inp-manager-id')?.value || null,
    date_entretien: getValueByName('date_entretien'),
    mission_ponctuelles: getValueByName('missions_ponctuelles'),
    niveau: niveauMap[getCheckedValue('niveau_expertise')] || null,
    commentaire_bilan: getValueByName('bilan_manager_s1'),
    commentaire_formation: getValueByName('formation_commentaire'),
    commentaire_libre: getValueByName('commentaire_final_collab'),
    date_signature_colab: getValueByName('sig_collab_date'),
    date_signature_manager: getValueByName('sig_manager_date'),
    performance,
    qcm,
    axes,
    formations
  };
}

/* =========================================================
   SUBMIT
========================================================= */



async function handleSubmit(e) {
  e.preventDefault();

  const payload = buildEntretienPayload();
  console.log('Payload envoyé :', payload);

  if (!payload.id_employe) {
    alert('Veuillez sélectionner un employé valide via le matricule.');
    return;
  }

  if (!payload.date_entretien) {
    alert("Veuillez renseigner la date de l'entretien.");
    return;
  }

  try {
    const response = await fetch('api/entretien_save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const raw = await response.text();
    console.log('Réponse brute API :', raw);

    let json = null;
    try {
      json = JSON.parse(raw);
    } catch (e) {
      throw new Error('Réponse non JSON du serveur : ' + raw);
    }

    if (!response.ok || !json.success) {
      throw new Error(json.message || 'Échec de l’enregistrement');
    }

    alert(`✅ Entretien enregistré avec succès.\nID : ${json.id_entretien}`);
  } catch (error) {
    console.error('Erreur sauvegarde entretien :', error);
    alert('Erreur serveur pendant la sauvegarde : ' + error.message);
  }
}