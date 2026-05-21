<?php
require '../auth/auth_check.php';
$pageTitle = 'Dashboard';
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

// Fetc Who logged in

$adminName = $_SESSION['admin_name'] ?? 'Admin';
$userType  = $_SESSION['user_type'] ?? '';

$where[] = "cases.submitted_by = '$adminName'";

$whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
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
        cases.submitted_by,
        cases.channel,
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

// fetch all clients for the add case modal
$clients_res = $pdo->query("SELECT token, name FROM clients ORDER BY name ASC");


?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Backyard Dashboard</h1>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCaseModal"  onclick="openAddClientModal()">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Add New Case
        </button>
    </div>
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

<!-- Add case manually modal -->
 <div id="addClientModal" class="modal">
  <div class="modal-content large">

    <h3 style="font-weight: bold;color:#e81a3b">Add New Client</h3>
<form method="POST" action="add_client.php">
      <div class="form-grid">
        <div>
          <label>Concerned Entity</label>
          <select>
            <option value="">-- Select Entity --</option>
            <!-- Add dynamically list of clients here -->
             <?php while($row = $clients_res->fetch(PDO::FETCH_ASSOC)): ?>
             <option value="<?php echo $row['token']; ?>"><?php echo $row['name']; ?></option>
             <?php endwhile; ?>          
          </select>
        </div>

        <div>
           <label class="form-label">Received Channel</label>
            <select name="channel" class="form-select" required>
                <option value="">-- Select Channel --</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="Toll free">Toll free</option>
                <option value="sms">SMS</option>
                <option value="email">Email</option>
            </select>
        </div>

        <div>
          <label class="form-label">Affiliation</label>
          <select name="affiliation" class="form-select">
            <option value="">-- Select Affiliation --</option>
              <option value="Employee">Employee</option>
              <option value="Client">Client</option>
              <option value="Supplier">Supplier</option>
              <option value="Other">Other</option>
          </select>
        </div>

        <div>
          <label class="form-label">Anonymity</label>
          <select name="anonymity" class="form-select">
            <option value="">-- Select Anonymity --</option>
              <option value="Anonymous">Anonymous</option>
              <option value="identifiable to BDO only">Identifiable to BDO only</option>
              <option value="Identifiable">Identifiable</option>
          </select>
        </div>

        <!-- Only whsitleblower fileds name, email, telephone appear when the selected field above is isdentifiable or identifiable to BDO only -->
        <div>
          <label class="form-label">Whistleblower Contact</label>
          <input type="text" name="whistleblower_name" class="form-control" placeholder="Whistleblower Name">
          <input type="text" name="whistleblower_email" class="form-control" placeholder="Whistleblower Email">
          <input type="text" name="whistleblower_phone" class="form-control" placeholder="Whistleblower Phone">
        </div>
     

        <div class="full">
          <label>When did the incident(s) take place?</label>
          <textarea name="when" rows="3"></textarea>
        </div>

        <div class="full">
          <label>Where did the incident(s) take place?</label>
          <textarea name="where" rows="3"></textarea>
        </div>

        <div class="full">
          <label>Which business division, department or site does your report concern?</label>
          <textarea name="which" rows="3"></textarea>
        </div>

        <div class="full">
          <label>Please provide a detailed description of the incident(s) you are reporting, including any relevant dates, times, and individuals involved.</label>
          <textarea name="description" rows="5"></textarea>
        </div>
        
      </div>

      <div class="modal-actions">
        <button type="submit" class="primary-btn">Save Client</button>
        <button type="button" class="btn" onclick="closeAddClientModal()">Cancel</button>
      </div>

    </form>
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

<script>
document.getElementById('identity_choice').addEventListener('change', function() {
    const container = document.getElementById('identifiable_fields');
    if (this.value !== 'Anonymous') {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
});


</script>
<script>
function openAddClientModal() {
  document.getElementById('addClientModal').style.display = 'flex';
}

function closeAddClientModal() {
  document.getElementById('addClientModal').style.display = 'none';
}
</script>
