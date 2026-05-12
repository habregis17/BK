<?php
// $pageheader= 'Cases';
require '../auth/auth_check.php';
require '../includes/header.php';
require '../includes/sidebar.php';
require '../../config/db.php';

function sortLink(string $column, string $label, string $currentSort, string $currentDir): string
{
    $dir = ($currentSort === $column && $currentDir === 'ASC') ? 'desc' : 'asc';
    $arrow = '';

    if ($currentSort === $column) {
        $arrow = $currentDir === 'ASC' ? '▲' : '▼';
    }

    $query = array_merge($_GET, [
        'sort' => $column,
        'dir'  => $dir,
        'page' => 1, // reset to first page on sort
    ]);

    return "<a href=\"?" . http_build_query($query) . "\" class=\"sort-link\">
              $label <span class=\"sort-arrow\">$arrow</span>
            </a>";
}
/* --------------------------
   INPUTS (FILTERS)
--------------------------- */
$page     = max(1, (int)($_GET['page'] ?? 1));

$perPageInput = filter_input(INPUT_GET, 'perPage', FILTER_VALIDATE_INT);

$perPage = in_array($perPageInput, [10, 20, 50, 100])
    ? $perPageInput
    : 10;

$offset  = ($page - 1) * $perPage;

$search      = trim($_GET['search'] ?? '');
$status      = $_GET['status'] ?? '';
$sensitivity = $_GET['sensitivity'] ?? '';
$identity_choice = $_GET['identity_choice'] ?? '';
$dateFrom    = $_GET['date_from'] ?? '';
$dateTo      = $_GET['date_to'] ?? '';

/* --------------------------
   WHERE CONDITIONS
--------------------------- */
$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(cases.casenumber LIKE :search OR clients.name LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($status !== '') {
    $where[] = "cases.status = :status";
    $params[':status'] = $status;
}

if ($sensitivity !== '') {
    $where[] = "cases.Case_sensitivity = :sensitivity";
    $params[':sensitivity'] = $sensitivity;
}

if ($identity_choice !== '') {
    $where[] = "cases.identity_choice = :identity_choice";
    $params[':identity_choice'] = $identity_choice;
}

if ($dateFrom !== '') {
    $where[] = "DATE(cases.submitted_at) >= :dateFrom";
    $params[':dateFrom'] = $dateFrom;
}

if ($dateTo !== '') {
    $where[] = "DATE(cases.submitted_at) <= :dateTo";
    $params[':dateTo'] = $dateTo;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* --------------------------
   TOTAL ROWS (FOR PAGINATION)
--------------------------- */
$countSql = "
    SELECT COUNT(*)
    FROM cases
    JOIN clients ON cases.client_token = clients.token
    $whereSQL
";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

/* --------------------------
   SORTING (SAFE WHITELIST)
--------------------------- */
$allowedSorts = [
  'casenumber',
  'client_name',
  'identity_choice',
  'status',
  'sensitivity',
  'submitted_at',
];

$sort = $_GET['sort'] ?? 'submitted_at';
$dir  = strtolower($_GET['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'submitted_at';
}

/* --------------------------
   FETCH CASES (PAGED)
--------------------------- */
$dataSql = "
    SELECT
        cases.id,
        cases.casenumber,
        cases.status,
        cases.Case_sensitivity,
        cases.submitted_at,
        cases.identity_choice,
        clients.name AS client_name
    FROM cases
    JOIN clients ON cases.client_token = clients.token
    $whereSQL
    ORDER BY $sort $dir
    LIMIT :limit OFFSET :offset
";

$dataStmt = $pdo->prepare($dataSql);
foreach ($params as $k => $v) {
    $dataStmt->bindValue($k, $v);
}
$dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();

$cases = $dataStmt->fetchAll(PDO::FETCH_ASSOC);



?>

<h1>Cases</h1>
<p class="muted">Cases at a Glance</p>
<form method="GET" class="filter-bar">
  <div class="filter-group">
    <label for="search">Search</label>
    <input id="search" type="text" name="search"
           value="<?= htmlspecialchars($search) ?>"
           placeholder="Case # or client">
  </div>

  <div class="filter-group">
    <label for="date_from">From</label>
    <input id="date_from" type="date" name="date_from"
           value="<?= htmlspecialchars($dateFrom) ?>">
  </div>

  <div class="filter-group">
    <label for="date_to">To</label>
    <input id="date_to" type="date" name="date_to"
           value="<?= htmlspecialchars($dateTo) ?>">
  </div>

  <div class="filter-group">
    <label for="status">Status</label>
    <select id="status" name="status">
      <option value="">All</option>
            <option value="New" <?= $status==='New'?'selected':'' ?>>New</option>
      <option value="Pending" <?= $status==='Pending'?'selected':'' ?>>Pending</option>
      <option value="Closed" <?= $status==='Closed'?'selected':'' ?>>Closed</option>
    </select>
  </div>

  <div class="filter-group">
    <label for="sensitivity">Sensitivity</label>
    <select id="sensitivity" name="sensitivity">
      <option value="">All</option>
      <option value="Low" <?= $sensitivity==='Low'?'selected':'' ?>>Low</option>
      <option value="Medium" <?= $sensitivity==='Medium'?'selected':'' ?>>Medium</option>
      <option value="High" <?= $sensitivity==='High'?'selected':'' ?>>High</option>
    </select>
  </div>

  <div class="filter-group">
    <label for="identity_choice">Anonymity</label>
    <select id="identity_choice" name="identity_choice">
      <option value="">All</option>
      <option value="Anonymous" <?= $identity_choice==='Anonymous'?'selected':'' ?>>Anonymous</option>
      <option value="identifiable" <?= $identity_choice==='identifiable'?'selected':'' ?>>Identifiable</option>
      <option value="Identifiable to BDO only" <?= $identity_choice==='Identifiable to BDO only'?'selected':'' ?>>Identifiable to BDO only</option>
    </select>
  </div>

  <div class="filter-actions filter-group">
    <button type="submit">Apply</button>
    <a href="index.php" class="clear-btn">Clear</a>
  </div>

</form>


<!-- ✅ EXPORT BUTTONS GO HERE (WE WILL INTEGRATE LATER) -->
<div class="export-bar">
  
  <a href="export_cases_pdf.php?<?= http_build_query($_GET) ?>" class="export-btn pdf" target="_blank" onclick="showExportLoading('PDF'); return false;">
    <i class="fas fa-file-pdf"></i> Export PDF
  </a>

  <a href="export_cases_excel.php?<?= http_build_query($_GET) ?>" class="export-btn excel" target="_blank" onclick="showExportLoading('Excel'); return false;">
    <i class="fas fa-file-excel"></i> Export Excel
  </a>

</div>
<table class="data-table">
  <thead>
    <tr>
      <th>No</th>
      <th><?= sortLink('casenumber', 'Case Number', $sort, $dir) ?></th>
      <th><?= sortLink('client_name', 'Client Name', $sort, $dir) ?></th>
      <th><?= sortLink('identity_choice', 'Anonymity', $sort, $dir) ?></th>
      <th><?= sortLink('Case_sensitivity', 'Sensitivity', $sort, $dir) ?></th>
      <th><?= sortLink('status', 'Status', $sort, $dir) ?></th>
      <th><?= sortLink('submitted_at', 'Submitted At', $sort, $dir) ?></th>
      <th>Actions</th>
    </tr>
  <tbody>
    <?php if (!$cases): ?>
      <tr><td colspan="7">No cases found</td></tr>
    <?php endif; ?>

    <?php
      $rowNumber = $offset + 1;
      foreach ($cases as $case):
    ?>
    <tr>
      <td><?= $rowNumber++ ?></td>
      <td><?= htmlspecialchars($case['casenumber']) ?></td>
      <td><?= htmlspecialchars($case['client_name']) ?></td>
      <td><?= htmlspecialchars($case['identity_choice']) ?></td>
      <td><?= htmlspecialchars($case['Case_sensitivity']) ?></td>
      <td><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $case['status'])) ?>"><?= htmlspecialchars($case['status']) ?></span></td>
      <td><?= date('Y-m-d', strtotime($case['submitted_at'])) ?></td>
      <td>
        <a href="view.php?casenumber=<?= urlencode($case['casenumber']) ?>" class="btn-view">
          <i class="fas fa-eye"></i>View
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<div class="pagination-bar">
  <span>
    Showing <?= $offset + 1 ?>–<?= min($offset + $perPage, $totalRows) ?>
    of <?= $totalRows ?>
  </span>
<!-- Pagination -->
  <div class="pagination">
    <?php for ($p=1; $p<=$totalPages; $p++): ?>
      <a
        href="?<?= http_build_query(array_merge($_GET,['page' => $p])) ?>"
        class="<?= $p==$page ? 'active' : '' ?>"
      >
        <?= $p ?>
      </a>
    <?php endfor; ?>
  </div>

  <form method="GET" class="per-page">
    <?php foreach ($_GET as $k => $v):
      if ($k !== 'perPage'): ?>
        <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
    <?php endif; endforeach; ?>

    <select name="perPage" onchange="this.form.submit()">
      <?php foreach ([10,20,50,100] as $n): ?>
        <option value="<?= $n ?>" <?= $perPage==$n?'selected':'' ?>><?= $n ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="loading-modal">
  <div class="loading-content">
    <div class="loading-spinner"></div>
    <h3 id="loading-title">Generating...</h3>
    <p id="loading-message">Please wait while we prepare your export.</p>
    <div class="progress-bar">
      <div class="progress-fill" id="progress-fill"></div>
    </div>
  </div>
</div>

<script>
function showExportLoading(type) {
  const modal = document.getElementById('loading-modal');
  const title = document.getElementById('loading-title');
  const message = document.getElementById('loading-message');
  const progress = document.getElementById('progress-fill');
  
  title.textContent = 'Generating ' + type + '...';
  message.textContent = 'Please wait while we prepare your export.';
  progress.style.width = '0%';
  
  modal.style.display = 'flex';
  
  // Animate progress
  let width = 0;
  const interval = setInterval(function() {
    if (width >= 90) {
      clearInterval(interval);
    } else {
      width += Math.random() * 15;
      if (width > 90) width = 90;
      progress.style.width = width + '%';
    }
  }, 200);
  
  // Build the URL with current filters
  const params = new URLSearchParams(window.location.search);
  const url = type === 'PDF' 
    ? 'export_cases_pdf.php?' + params.toString()
    : 'export_cases_excel.php?' + params.toString();
  
  // Fetch the file and download it
  fetch(url, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
    .then(response => {
      if (!response.ok) throw new Error('Export failed');
      // Extract filename from Content-Disposition header if available
      const contentDisposition = response.headers.get('Content-Disposition');
      let filename = 'Cases_Export.' + (type === 'PDF' ? 'pdf' : 'xlsx');
      
      if (contentDisposition) {
        const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
        if (filenameMatch) {
          filename = filenameMatch[1].replace(/['"]/g, '');
        }
      }
      
      return response.blob().then(blob => ({ blob, filename }));
    })
    .then(data => {
      // Create a download link with proper filename
      const downloadLink = document.createElement('a');
      downloadLink.href = window.URL.createObjectURL(data.blob);
      downloadLink.download = data.filename;
      downloadLink.style.display = 'none';
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
      
      // Close modal when file is ready
      clearInterval(interval);
      progress.style.width = '100%';
      title.textContent = type + ' Ready!';
      message.textContent = 'Your file is downloading...';
      
      setTimeout(function() {
        modal.style.display = 'none';
      }, 1500);
    })
    .catch(error => {
      clearInterval(interval);
      modal.style.display = 'none';
      alert('Export failed. Please try again.');
      console.error('Export error:', error);
    });
}
</script>

<?php require '../includes/footer.php'; ?>
