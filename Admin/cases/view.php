<?php
require '../auth/auth_check.php';
$pageTitle = 'Case View';
require '../includes/header.php';
require '../includes/sidebar.php';
require '../../config/db.php';
$rawFiles = $case['files'] ?? '';
$files = [];

if ($rawFiles) {
    $decoded = json_decode($rawFiles, true);
    if (is_array($decoded)) {
        $files = $decoded;
    } else {
        $files = array_filter(explode(',', $rawFiles)); // fallback if not JSON
    }
}


$caseNumber = $_GET['casenumber'] ?? '';
if (!$caseNumber) {
  die("Invalid case number.");
}

$stmt = $pdo->prepare("SELECT cases.*, clients.name AS client_name 
                       FROM cases 
                       JOIN clients ON cases.client_token = clients.token 
                       WHERE cases.casenumber = ?");
$stmt->execute([$caseNumber]);
$case = $stmt->fetch();

if (!$case) {
  die("Case not found.");
}

/* Fetch comments */
$commentsStmt = $pdo->prepare("
  SELECT * FROM case_comments
  WHERE case_id = ?
  ORDER BY created_at ASC
");
$commentsStmt->execute([$case['casenumber']]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

$closureComment = '';
if (!empty($comments)) {
  $lastComment = end($comments);
  $closureComment = $lastComment['comment'] ?? '';
  reset($comments);
}

/* Fetch audit trail */
$auditStmt = $pdo->prepare("
  SELECT * FROM case_status_audit
  WHERE case_id = ?
  ORDER BY changed_at ASC
");
$auditStmt->execute([$case['casenumber']]);
$audits = $auditStmt->fetchAll(PDO::FETCH_ASSOC);

/* Fetch reporter information */

$identity = strtolower(trim($case['identity_choice']));
$showPII = in_array($identity, [
  'identifiable',
  'identified',
  'identifiable to bdo only'
]);


?>

<?php if (!empty($_SESSION['success'])): ?>
  <div id="success-toast" class="toast success">
    <i class="fas fa-check-circle"></i>
    <span><?= htmlspecialchars($_SESSION['success']) ?></span>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>


<div class="content">

<?php if (!empty($_SESSION['error'])): ?>
  <div class="alert error">
    <?= htmlspecialchars($_SESSION['error']) ?>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="case-summary">
  <!-- Add a breadcrumb to return to all cases -->
   <div class="breadcrumb">
    <a href="index.php"><i class="fas fa-arrow-left"></i> Back </a>
  </div>
    
  <div class="summary-left">
    <h2>CASE ID : <?= htmlspecialchars($case['casenumber']) ?></h2>

    <div class="badges">
      <span class="badge status <?= strtolower($case['status']) ?>">
        <?= htmlspecialchars($case['status']) ?>
      </span>

      <span class="badge sensitivity <?= strtolower($case['Case_sensitivity']) ?>">
        <?= htmlspecialchars($case['Case_sensitivity']) ?>
      </span>
    </div>
  </div>

  <div class="summary-right">
    <div class="export-bar">
    <a href="export_pdf.php?casenumber=<?= urlencode($case['casenumber']) ?>" target="_blank" id="export-pdf-btn" onclick="showLoading('PDF'); return false;">
      <i class="fas fa-file-pdf"></i> Export PDF
    </a>
    <a href="export_excel.php?casenumber=<?= urlencode($case['casenumber']) ?>" target="_blank" id="export-excel-btn" onclick="showLoading('Excel'); return false;">
      <i class="fas fa-file-excel"></i> Export Excel
    </a>
    </div>

  </div>

</div>

<div class="overview-grid">

  <div class="overview-card">
    <label>Concerned Entity</label>
    <span><?= htmlspecialchars($case['client_name']) ?></span>
  </div>

  <div class="overview-card">
    <label>Reporter</label>
    <b><span><?= htmlspecialchars($case['identity_choice']) ?></span></b>
    
<?php if ($showPII): ?>

  <?php if (!empty($case['full_name'])): ?>
    <div class="overview-card">
      <label>Reporter Name</label>
      <span><?= htmlspecialchars($case['full_name']) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($case['email'])): ?>
    <div class="overview-card">
      <label>Email</label>
      <span><?= htmlspecialchars($case['email']) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($case['phone'])): ?>
    <div class="overview-card">
      <label>Telephone</label>
      <span><?= htmlspecialchars($case['phone']) ?></span>
    </div>
  <?php endif; ?>

<?php endif; ?>

  </div>

  <!-- <div class="overview-card">
    <label>Case Manager</label>
    <span><?= htmlspecialchars($case['Case_manager']) ?></span>
  </div> -->

  <div class="overview-card">
    <label>Submitted</label>
    <span><?= htmlspecialchars($case['submitted_at']) ?></span>
  </div>

</div>

<div class="detail-section">

  <h3>Case Description</h3>

  <p><?= nl2br(htmlspecialchars($case['incident_description'])) ?></p>

  <div class="detail-split">
    <div>
      <label>When</label>
      <p><?= htmlspecialchars($case['incident_when']) ?></p>
    </div>

    <div>
      <label>Where</label>
      <p><?= htmlspecialchars($case['incident_where']) ?></p>
    </div>

    <div>
      <label>Division</label>
      <p><?= htmlspecialchars($case['incident_division']) ?></p>
    </div>
  </div>

</div>

<div class="detail-card">
  <h3>Attachments</h3>
   <?php
$files = json_decode($case['files'], true); // Decode JSON string to PHP array

if (is_array($files) && count($files) > 0): ?>
  <label><strong>Attached File(s)</strong></label>
  <div class="file-list">
    <?php foreach ($files as $file): 
      $path = "../../Submit/" . htmlspecialchars($file);
      $filename = basename($file);
      $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

      // Choose icon based on file type
      switch ($ext) {
        case 'pdf': $icon = '📄'; break;
        case 'doc':
        case 'docx': $icon = '📝'; break;
        case 'xls':
        case 'xlsx': $icon = '📊'; break;
        case 'jpg':
        case 'jpeg':
        case 'png': $icon = '🖼️'; break;
        case 'zip':
        case 'rar': $icon = '🗜️'; break;
        case 'whiteboard': $icon = '🧭'; break;
        default: $icon = '📁'; break;
      }
    ?>
      <div style="margin: 5px 0;">
        <a href="<?= $path ?>" target="_blank" style="text-decoration: none;">
          <?= $icon ?> <?= $filename ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p><em>No files attached.</em></p>
<?php endif; ?>
</div>

<div class="detail-card">
  <h3>Activity Timeline</h3>

  <!-- <?php foreach ($comments as $c): ?>
    <div class="timeline-item <?= $c['comment_type'] ?>">
      <strong><?= htmlspecialchars($c['created_by']) ?></strong>
      <span class="time"><?= htmlspecialchars($c['created_at']) ?></span>
      <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
    </div>
  <?php endforeach; ?> -->

  <?php foreach ($audits as $a): ?>
    <div class="timeline-item audit">
      <strong><?= htmlspecialchars($a['changed_by']) ?></strong>
      changed status from
      <em><?= $a['old_status'] ?></em> to <em><?= $a['new_status'] ?></em>
      <span class="time"><?= $a['changed_at'] ?></span>
    </div>
  <?php endforeach; ?>
</div>

<div class="admin-panel">

  <h3>Administrative Actions</h3>
  
<?php $isClosed = ($case['status'] === 'Closed'); ?>
  
<?php if ($isClosed): ?>
  <div class="readonly-banner">
    <i class="fas fa-lock"></i>
    <span>This case is closed and cannot be edited</span>
  </div>
<?php endif; ?>
  
  <form method="POST" action="update_case_status.php">
    <input type="hidden" name="case_id" value="<?= $case['casenumber'] ?>">

    <label>Status</label>
    <select name="status" id="status-select" onchange="toggleClosureComment()" <?= $isClosed ? 'disabled' : '' ?>>
      <option <?= $case['status']=='Pending'?'selected':'' ?>>Pending</option>
      <option <?= $case['status']=='Closed'?'selected':'' ?>>Closed</option>
    </select>

    <label>Sensitivity</label>
    <select name="sensitivity" id="sensitivity-select" <?= $isClosed ? 'disabled' : '' ?>>
      <option <?= $case['Case_sensitivity']=='Low'?'selected':'' ?>>Low</option>
      <option <?= $case['Case_sensitivity']=='Medium'?'selected':'' ?>>Medium</option>
      <option <?= $case['Case_sensitivity']=='High'?'selected':'' ?>>High</option>
      <option <?= $case['Case_sensitivity']=='Critical'?'selected':'' ?>>Critical</option>
    </select>

    <label>Relevance</label>
    <select name="relevance" id="relevance-select" <?= $isClosed ? 'disabled' : '' ?>>
      <option <?= $case['Case_relevance']=='Relevant'?'selected':'' ?>>Relevant</option>
      <option <?= $case['Case_relevance']=='Irrelevant'?'selected':'' ?>>Irrelevant</option>
      <option <?= $case['Case_relevance']=='Empty'?'selected':'' ?>>Empty</option>
      <option <?= $case['Case_relevance']=='Core Banking Operation Issue'?'selected':'' ?>>Core Banking Operation Issue</option>
    </select>

    <div id="closure-comment" style="display:none;">
      <label>Closure Comment (Required)</label>
      <textarea name="closure_comment" <?= $isClosed ? 'readonly' : '' ?>><?= $closureComment ?></textarea>
    </div>

<?php if (!$isClosed): ?>
    <button class="primary-btn">Update Case</button>
<?php else: ?>
    <button class="primary-btn" disabled>Update Case</button>
<?php endif; ?>
  </form>

</div>



<script>
function toggleClosureComment() {
  const status = document.getElementById('status-select').value;
  document.getElementById('closure-comment').style.display =
    (status === 'Closed') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleClosureComment);
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const toast = document.getElementById('success-toast');
  if (!toast) return;

  setTimeout(() => {
    toast.classList.add('fade-out');
  }, 4500);

  setTimeout(() => {
    toast.remove();
  }, 5000);
});
</script>

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
function showLoading(type) {
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
  
  // Determine the URL based on type
  const casenumber = '<?= urlencode($case["casenumber"]) ?>';
  const url = type === 'PDF' 
    ? 'export_pdf.php?casenumber=' + casenumber 
    : 'export_excel.php?casenumber=' + casenumber;
  
  // Fetch the file and open it
  fetch(url, {
  headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(response => {
  if (!response.ok) throw new Error('Export failed');
  return response.blob().then(blob => ({ blob, response }));
})
.then(({ blob, response }) => {
  let filename = 'Case_' + casenumber + (type === 'PDF' ? '.pdf' : '.xlsx');

  const cd = response.headers.get('Content-Disposition');
  if (cd) {
    const match = cd.match(/filename=\"?([^\";]+)\"?/);
    if (match) filename = match[1];
  }

  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);

  modal.style.display = 'none';
})
.catch(err => {
  modal.style.display = 'none';
  alert('Export failed');
  console.error(err);
});
}
</script>

<?php require '../includes/footer.php'; ?>