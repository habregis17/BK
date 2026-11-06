<?php
// Force error reporting display for debugging
error_reporting(E_ALL);
m: ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Force fatal error display
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== NULL) {
        echo "<pre>";
        print_r($error);
        echo "</pre>";
    }
});


require '../auth/auth_check.php';
$pageTitle = 'Case View';
require '../includes/header.php';
require '../includes/sidebar.php';
require '../../config/db.php';

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
/* Fetch Previous and Next case numbers based on chronological submission order */
$prevCase = null;
$nextCase = null;

if (isset($case['id'])) {
    // Previous = Case submitted BEFORE (lower ID)
    $prevStmt = $pdo->prepare("SELECT casenumber FROM cases WHERE id < ? ORDER BY id DESC LIMIT 1");
    $prevStmt->execute([$case['id']]);
    $prevCase = $prevStmt->fetch(PDO::FETCH_ASSOC);

    // Next = Case submitted AFTER (higher ID)
    $nextStmt = $pdo->prepare("SELECT casenumber FROM cases WHERE id > ? ORDER BY id ASC LIMIT 1");
    $nextStmt->execute([$case['id']]);
    $nextCase = $nextStmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Fallback if 'id' column is missing, using submitted_at timestamp
    // Previous = Submitted BEFORE (earlier timestamp)
    $prevStmt = $pdo->prepare("SELECT casenumber FROM cases WHERE submitted_at < ? ORDER BY submitted_at DESC LIMIT 1");
    $prevStmt->execute([$case['submitted_at']]);
    $prevCase = $prevStmt->fetch(PDO::FETCH_ASSOC);

    // Next = Submitted AFTER (later timestamp)
    $nextStmt = $pdo->prepare("SELECT casenumber FROM cases WHERE submitted_at > ? ORDER BY submitted_at ASC LIMIT 1");
    $nextStmt->execute([$case['submitted_at']]);
    $nextCase = $nextStmt->fetch(PDO::FETCH_ASSOC);
}

$rawFiles = $case['files'] ?? '';
$files = [];

if ($rawFiles) {
    $decoded = json_decode($rawFiles, true);
    if (is_array($decoded)) {
        $files = $decoded;
    } else {
        $files = array_filter(explode(',', $rawFiles));
    }
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

$channel = $case['channel'] ;
function getChannelIcon($channel) {
    switch ($channel) {
        case 'Website': return 'fa-globe';
        case 'Email': return 'fa-envelope';
        case 'Call Center': return 'fa-phone';
        case 'Walk-in': return 'fa-person-walking';
        case 'WhatsApp': return 'fa-whatsapp';
        case 'SMS': return 'fa-sms';
        case 'Other': return 'fa-ellipsis-h';
        default: return 'fa-share-nodes';
    }
}

$languages = $case['language'] ?? '';   
function getLanguageIcon($languages) {
  switch ($languages) {
    default:
      return 'fa-language';
  }
}

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

<!-- Redesigned Navigation Bar (Back Button & Previous/Next Switcher) -->
<div class="case-nav-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <a href="index.php" class="nav-btn back-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; background: #333333; color: #FFFFFF; border: 2px solid #e0e0e0; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.95rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s ease;">
    <i class="fas fa-arrow-left" style="color: #FFFFFF;"></i> Back to Cases
  </a>

  <div class="case-switcher" style="display: flex; align-items: center; gap: 8px;">
    <?php if ($prevCase): ?>
      <a href="view.php?casenumber=<?= urlencode($prevCase['casenumber']) ?>" class="nav-btn" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: #fff; color: #333333; border: 2px solid #e0e0e0; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s ease;" title="Previous Case">
        <i class="fas fa-chevron-left"></i> Previous
      </a>
    <?php else: ?>
      <span class="nav-btn disabled" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: #f9f9f9; color: #bbb; border: 2px solid #eee; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: not-allowed;">
        <i class="fas fa-chevron-left"></i> Previous
      </span>
    <?php endif; ?>

    <span style="font-size: 0.85rem; color: #777; font-weight: 600; padding: 0 6px; text-transform: uppercase; letter-spacing: 0.5px;">
      Quick Nav
    </span>

    <?php if ($nextCase): ?>
      <a href="view.php?casenumber=<?= urlencode($nextCase['casenumber']) ?>" class="nav-btn" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: #fff; color: #333333; border: 2px solid #e0e0e0; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s ease;" title="Next Case">
        Next <i class="fas fa-chevron-right"></i>
      </a>
    <?php else: ?>
      <span class="nav-btn disabled" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: #f9f9f9; color: #bbb; border: 2px solid #eee; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: not-allowed;">
        Next <i class="fas fa-chevron-right"></i>
      </span>
    <?php endif; ?>
  </div>
</div>

<style>
  .case-nav-bar a.nav-btn:hover {
    background-color: #ED1A3B !important;
    color: #fff !important;
    border-color: #ED1A3B !important;
  }
  .case-nav-bar a.nav-btn:hover i {
    color: #fff !important;
  }
</style>

<div class="case-summary">
  <div class="summary-left">

    <h2>
      <i class="fa-solid fa-hashtag"></i>
      CASE ID: <?= htmlspecialchars($case['casenumber']) ?>
    </h2>

    <div class="badges">

      <!-- STATUS -->
      <span class="badge status <?= strtolower($case['status']) ?>">
        <i class="fa-solid fa-circle"></i>
        <?= htmlspecialchars($case['status']) ?>
      </span>

        <!-- SENSITIVITY -->
      <span class="badge sensitivity <?= strtolower($case['Case_sensitivity']) ?>">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <?= htmlspecialchars($case['Case_sensitivity']) ?>
      </span>

      <!-- Language -->
      <span class="badge language">
        <i class="fa-solid <?= getLanguageIcon($languages) ?>"></i>
        <?= htmlspecialchars($languages ?: 'Unknown') ?>
      </span>

      <!-- Channel -->
      <span class="badge channel">
        <i class="fa-solid <?= getChannelIcon($channel) ?>"></i>
        <?= htmlspecialchars($channel ?: 'Unknown') ?>
      </span>

      <!-- AGENT -->
      <span class="badge agent">
        <i class="fa-solid fa-user"></i>
        <?= htmlspecialchars($case['submitted_by'] ?: 'System') ?>
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
    <span><?= htmlspecialchars($case['identity_choice']) ?></span>
    
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
if (is_array($files) && count($files) > 0): ?>
  <label><strong>Attached File(s)</strong></label>
  <div class="file-list">
    <?php foreach ($files as $file): 
      $path = "../../Submit/" . htmlspecialchars($file);
      $filename = basename($file);
      $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

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
  
  <form method="POST" action="update_case_status.php" id="caseUpdateForm">
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
      <textarea name="closure_comment" id="closure_comment" <?= $isClosed ? 'readonly' : '' ?>><?= $closureComment ?></textarea>
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
  
  const casenumber = '<?= urlencode($case["casenumber"]) ?>';
  const url = type === 'PDF' 
    ? 'export_pdf.php?casenumber=' + casenumber 
    : 'export_excel.php?casenumber=' + casenumber;
  
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  const adminForm = document.querySelector('.admin-panel form');
  if (adminForm) {
    adminForm.addEventListener('submit', function (e) {
      const relevanceSelect = document.getElementById('relevance-select');
      const statusSelect = document.getElementById('status-select');
      
      if (relevanceSelect && relevanceSelect.value === 'Core Banking Operation Issue' && statusSelect && statusSelect.value === 'Closed') {
        const warningMessage = "WARNING: You have selected 'Core Banking Operation Issue'.\n\nProceeding will update this case and automatically dispatch an email notification to BK Support Team. Do you want to proceed?";
        
        if (!confirm(warningMessage)) {
          e.preventDefault(); // Cancel form submission if reviewer clicks 'Cancel'
        }
      }
    });
  }
});

</script>
<!-- predefine Comment on Core banking issue -->
 <script>
document.addEventListener("DOMContentLoaded", function() {
    // Replace these IDs with the actual IDs of your dropdown and textarea in view.php
    const relevanceDropdown = document.getElementById("relevance-select"); 
    const commentField = document.getElementById("closure_comment");

    if (relevanceDropdown && commentField) {
        relevanceDropdown.addEventListener("change", function() {
            // Check if the selected option matches Core Banking
            if (this.value === "Core Banking Operation Issue") {
                // Set your predefined template text
                commentField.value = 
                    "Case identified as Core Banking Operation Issue and routed to BK Support Team";
            }
            else if (this.value === "Irrelevant") {
                // Set your predefined template text
                commentField.value = 
                    "Case falls outside whistleblower Scope";
            } 
            else if(this.value === "Empty"){
              // Set your predefined template text
              commentField.value=
                    "Case closed due to missing or insufficient details"
            }
            else if(this.value === "Relevant"){
              // Set your predefined template text
              commentField.value=
                    "Case verified as a valid whistleblower issue. Documented and escalated to RAS Team for formal review."
            }
            else {
              commentField.value="Case verified as a valid whistleblower issue. Documented and escalated to RAS Team for formal review.";
            }
           
        });
    }
});
</script>
<!-- Progress & Success Modal Overlay -->
<div id="uploadProgressModal" style="display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(2px); align-items: center; justify-content: center;">
    <div style="background: #ffffff; padding: 30px; border-radius: 10px; width: 400px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div id="progressSpinnerArea">
            <h3 id="progressTitle" style="margin-top: 0; color: #333; font-size: 1.2rem;">Processing Case Closure</h3>
            <p id="progressStatusText" style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Generating PDF report and dispatching email via Office 365</p>
            
            <!-- Progress Bar Container -->
            <div style="background: #e9ecef; border-radius: 20px; height: 12px; width: 100%; overflow: hidden; margin-bottom: 15px;">
                <div id="progressBarFill" style="background: #E81A3B; height: 100%; width: 0%; transition: width 0.4s ease-in-out;"></div>
            </div>
        </div>

        <!-- Success Checkmark Area (Hidden initially) -->
        <div id="successCheckmarkArea" style="display: none;">
            <div style="width: 60px; height: 60px; background: #d4edda; color: #155724; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 15px auto;">✓</div>
            <h3 style="color: #155724; margin: 0 0 10px 0;">Case Closed Successfully!</h3>
            <p style="color: #666; font-size: 0.9rem; margin: 0;">Reloading Case</p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("#caseUpdateForm"); // Replace with your actual form ID or selector
    if (!form) return;

    form.addEventListener("submit", function(e) {
        e.preventDefault(); // Stop standard form submission

        // Show modal
        const modal = document.getElementById("uploadProgressModal");
        const progressBar = document.getElementById("progressBarFill");
        const statusText = document.getElementById("progressStatusText");
        modal.style.display = "flex";

        // Simulate smooth progress steps while waiting for server response
        progressBar.style.width = "25%";
        statusText.innerText = "Building Case report PDF";

        setTimeout(() => { progressBar.style.width = "50%"; statusText.innerText = "Compiling uploaded evidence attachments"; }, 600);
        setTimeout(() => { progressBar.style.width = "75%"; statusText.innerText = "Connecting to Office 365"; }, 1500);

        // Submit data via Fetch API
        const formData = new FormData(form);
        formData.append("is_ajax", "1"); // Flag for backend to return JSON

        fetch(form.action || "update_case_status.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Complete progress bar to 100%
                progressBar.style.width = "100%";
                progressBar.style.background = "#28a745";
                
                setTimeout(() => {
                    // Hide spinner, show green checkmark
                    document.getElementById("progressSpinnerArea").style.display = "none";
                    document.getElementById("successCheckmarkArea").style.display = "block";
                    
                    // Reload page after brief pause
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                }, 500);
            } else {
                modal.style.display = "none";
                alert("Error: " + (data.error || "Failed to update case."));
            }
        })
        .catch(error => {
            modal.style.display = "none";
            console.error("Error:", error);
            alert("An unexpected network or server error occurred.");
        });
    });
});
</script>

<?php require '../includes/footer.php'; ?>