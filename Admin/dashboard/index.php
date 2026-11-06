<?php
require '../auth/auth_check.php';
$pageTitle = 'Whistleblower Case Insights & Analytics';
require '../includes/header.php';
require '../includes/sidebar.php';
require '../../config/db.php';

// --- 1. FILTER LOGIC (Dates & Quarters) ---
$dateFrom = $_GET['date_from'] ?? '';
$dateTo   = $_GET['date_to']   ?? '';
$quarter  = $_GET['quarter']   ?? '';

if (!empty($quarter)) {
    $currentYear = date('Y');
    switch ($quarter) {
        case 'Q1': $dateFrom = "$currentYear-01-01"; $dateTo = "$currentYear-03-31"; break;
        case 'Q2': $dateFrom = "$currentYear-04-01"; $dateTo = "$currentYear-06-30"; break;
        case 'Q3': $dateFrom = "$currentYear-07-01"; $dateTo = "$currentYear-09-30"; break;
        case 'Q4': $dateFrom = "$currentYear-10-01"; $dateTo = "$currentYear-12-31"; break;
        case 'YTD': $dateFrom = "$currentYear-01-01"; $dateTo = date('Y-m-d'); break;
    }
}

$whereClauses = [];
$params = [];

if (!empty($dateFrom)) {
    $whereClauses[] = "DATE(submitted_at) >= :date_from";
    $params[':date_from'] = $dateFrom;
}
if (!empty($dateTo)) {
    $whereClauses[] = "DATE(submitted_at) <= :date_to";
    $params[':date_to'] = $dateTo;
}

$whereSql = !empty($whereClauses) ? " WHERE " . implode(" AND ", $whereClauses) : "";

// --- 2. STATS & ANALYTICS QUERIES WITH SAFE ERROR CATCHING ---
$totalCases = 0;
$stats = ['pending_cases' => 0, 'new_cases' => 0, 'closed_cases' => 0, 'rerouted_count' => 0];
$channelData = [];
$relevanceData = [];
$reroutedCases = [];
$trendRaw = [];
$dbErrorMsg = '';

try {
    // Total Cases
    $totalCasesStmt = $pdo->prepare("SELECT COUNT(*) FROM cases $whereSql");
    $totalCasesStmt->execute($params);
    $totalCases = (int)$totalCasesStmt->fetchColumn();
    $safeTotal = $totalCases > 0 ? $totalCases : 1;

    // Status Breakdown
    $statusStmt = $pdo->prepare("SELECT status, COUNT(*) as cnt FROM cases $whereSql GROUP BY status");
    $statusStmt->execute($params);
    $statusRaw = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stats['pending_cases'] = (int)($statusRaw['Pending'] ?? 0);
    $stats['new_cases']     = (int)($statusRaw['new'] ?? $statusRaw['New'] ?? 0);
    $stats['closed_cases']  = (int)($statusRaw['Closed'] ?? 0);

    // Channel Breakdown (Safely handling potential column name differences)
    // Tries 'channel' first; falls back gracefully if column differs
    try {
        $channelStmt = $pdo->prepare("SELECT channel, COUNT(*) as total FROM cases $whereSql GROUP BY channel ORDER BY total DESC");
        $channelStmt->execute($params);
        $channelData = $channelStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $channelStmt = $pdo->prepare("SELECT channel as channel, COUNT(*) as total FROM cases $whereSql GROUP BY channel ORDER BY total DESC");
        $channelStmt->execute($params);
        $channelData = $channelStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Relevance Breakdown
    $relevanceStmt = $pdo->prepare("SELECT Case_relevance, COUNT(*) as total FROM cases $whereSql GROUP BY Case_relevance ORDER BY total DESC");
    $relevanceStmt->execute($params);
    $relevanceData = $relevanceStmt->fetchAll(PDO::FETCH_ASSOC);

    // Module 1: Rerouted Cases (Sent to Partner)
    $reroutedWhere = !empty($whereSql) ? "$whereSql AND sent_to_partner = 'yes'" : " WHERE sent_to_partner = 'yes'";
    $reroutedStmt = $pdo->prepare("SELECT * FROM cases $reroutedWhere ORDER BY submitted_at DESC");
    $reroutedStmt->execute($params);
    $reroutedCases = $reroutedStmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['rerouted_count'] = count($reroutedCases);

    // Trend: Daily Submissions
    $trendStmt = $pdo->prepare("SELECT DATE(submitted_at) AS day, COUNT(*) AS total FROM cases $whereSql GROUP BY DATE(submitted_at) ORDER BY day ASC");
    $trendStmt->execute($params);
    $trendRaw = $trendStmt->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $e) {
    $dbErrorMsg = $e->getMessage();
}
$safeTotal = $totalCases > 0 ? $totalCases : 1;
?>

<!-- FontAwesome & Chart.js & html2pdf -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
  .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; color: #E81A3B; }
  .dashboard-header h1 { font-size: 2rem; margin: 0; color: #E81A3B; }
  .filter-bar { background: #f8f9fa; padding: 15px 20px; border-radius: 8px; border: 1px solid #e3e6f0; margin-bottom: 25px; }
  .filter-form { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
  .filter-group { display: flex; flex-direction: column; gap: 5px; }
  .filter-group label { font-size: 0.85rem; font-weight: 600; color: #5b6e7f; }
  .filter-group input, .filter-group select { padding: 7px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.9rem; }
  .btn-filter { background: #333; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: 600; }
  
  .analytics-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
  .analytics-card { background: #fff; border-radius: 8px; padding: 20px; border: 1px solid #e3e6f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
  .analytics-card.full-width { grid-column: span 3; }
  .analytics-card h4 { margin-top: 0; color: #333; font-size: 1rem; border-bottom: 2px solid #f0f0f0; padding-bottom: 12px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
  
  .breakdown-row { margin-bottom: 15px; }
  .breakdown-info { display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 600; color: #333; margin-bottom: 5px; align-items: center; }
  .breakdown-info i { color: #E81A3B; width: 20px; text-align: center; margin-right: 8px; }
  .progress-track { background: #e9ecef; border-radius: 10px; height: 10px; width: 100%; overflow: hidden; }
  .progress-bar-fill { height: 100%; border-radius: 10px; transition: width 0.4s ease; }
  
  .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 0.9rem; }
  .data-table th, .data-table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
  .data-table th { background: #f8f9fa; color: #5b6e7f; font-weight: 600; }
  .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
  .badge-danger { background: #f8d7da; color: #721c24; }
  .badge-success { background: #d4edda; color: #155724; }
  .error-banner { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
</style>

<div id="dashboardReport">
  <div class="dashboard-header">
    <div>
      <h1 style="margin:0; color:#333;">Bank of Kigali Plc Case Analytics Hub</h1>
    </div>
  </div>

  <?php if (!empty($dbErrorMsg)): ?>
    <div class="error-banner">
      <strong>Database Error Encountered on Production:</strong> <?= htmlspecialchars($dbErrorMsg) ?>
    </div>
  <?php endif; ?>

  <!-- GLOBAL FILTER BAR -->
  <div class="filter-bar">
    <form method="GET" action="" class="filter-form">
      <div class="filter-group">
        <label>Date From</label>
        <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
      </div>
      <div class="filter-group">
        <label>Date To</label>
        <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
      </div>
      <div class="filter-group">
        <label>Quarter Preset</label>
        <select name="quarter" onchange="this.form.submit()">
          <option value="">-- Select Period --</option>
          <option value="Q1" <?= $quarter === 'Q1' ? 'selected' : '' ?>>Q1 (Jan - Mar)</option>
          <option value="Q2" <?= $quarter === 'Q2' ? 'selected' : '' ?>>Q2 (Apr - Jun)</option>
          <option value="Q3" <?= $quarter === 'Q3' ? 'selected' : '' ?>>Q3 (Jul - Sep)</option>
          <option value="Q4" <?= $quarter === 'Q4' ? 'selected' : '' ?>>Q4 (Oct - Dec)</option>
          <option value="YTD" <?= $quarter === 'YTD' ? 'selected' : '' ?>>Year to Date</option>
        </select>
      </div>
      <button type="submit" class="btn-filter">Apply Filter</button>
      <a href="dashboard.php" style="padding: 8px 12px; color: #666; text-decoration: none; font-size: 0.9rem;">Reset</a>
    </form>
  </div>

  <!-- KPI SUMMARY CARDS -->
  <div class="stats-grid" style="margin-bottom:25px;">
    <div class="stat-card">
      <span class="label">Total Cases Received</span>
      <span class="value"><?= $totalCases ?></span>
    </div>
    <div class="stat-card warning">
      <span class="label">Pending Action</span>
      <span class="value"><?= $stats['pending_cases'] ?></span>
    </div>
    <div class="stat-card info">
      <span class="label">New Submissions</span>
      <span class="value"><?= $stats['new_cases'] ?></span>
    </div>
    <div class="stat-card success">
      <span class="label">Closed Cases</span>
      <span class="value"><?= $stats['closed_cases'] ?></span>
    </div>
  </div>

  <!-- BREAKDOWN MODULES -->
  <div class="analytics-grid">
    
    <!-- MODULE: Cases by Channel -->
    <div class="analytics-card">
      <h4><span><i class="fa-solid fa-tower-broadcast" style="margin-right: 8px; color: #333333;"></i> Cases by Channel</span></h4>
      <div style="margin-top: 15px;">
        <?php if (empty($channelData)): ?>
          <p class="muted" style="text-align:center; padding:15px; color:#777;">No channel data available for this filter range.</p>
        <?php else: ?>
          <?php foreach ($channelData as $ch): 
            $count = (int)$ch['total'];
            $pct = round(($count / $safeTotal) * 100, 1);
            $channelNameLower = strtolower($ch['channel'] ?? 'website');
            $iconClass = 'fa-globe';
            if (str_contains($channelNameLower, 'whatsapp')) $iconClass = 'fa-brands fa-whatsapp';
            elseif (str_contains($channelNameLower, 'email')) $iconClass = 'fa-envelope';
            elseif (str_contains($channelNameLower, 'call center')) $iconClass = 'fa-phone';
            elseif (str_contains($channelNameLower, 'sms')) $iconClass = 'fa-sms';
          ?>
            <div class="breakdown-row">
              <div class="breakdown-info">
                <span><i class="fa-solid <?= $iconClass ?>"></i> <?= htmlspecialchars($ch['channel'] ?: 'Website') ?></span>
                <span><strong><?= $count ?></strong> <span style="color:#777; font-weight:normal;">(<?= $pct ?>%)</span></span>
              </div>
              <div class="progress-track">
                <div class="progress-bar-fill" style="width: <?= $pct ?>%; background: #008fd2;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- MODULE: Cases by Relevance -->
    <div class="analytics-card">
      <h4><span><i class="fa-solid circle-check" style="margin-right: 8px; color: #333333;"></i> Cases by Relevance</span></h4>
      <div style="margin-top: 15px;">
        <?php if (empty($relevanceData)): ?>
          <p class="muted" style="text-align:center; padding:15px; color:#777;">No relevance data available.</p>
        <?php else: ?>
          <?php foreach ($relevanceData as $rel): 
            $count = (int)$rel['total'];
            $pct = round(($count / $safeTotal) * 100, 1);
            $relName = $rel['Case_relevance'] ?: 'Unassigned';
            $barColor = '#5b6e7f';
            if (str_contains(strtolower($relName), 'relevant')) { $barColor = '#009966'; }
            elseif (str_contains(strtolower($relName), 'core')) { $barColor = '#008fd2'; }
            elseif (str_contains(strtolower($relName), 'irrelevant')) { $barColor = '#98002e'; }
          ?>
            <div class="breakdown-row">
              <div class="breakdown-info">
                <span><i class="fa-solid fa-file-lines"></i> <?= htmlspecialchars($relName) ?></span>
                <span><strong><?= $count ?></strong> <span style="color:#777; font-weight:normal;">(<?= $pct ?>%)</span></span>
              </div>
              <div class="progress-track">
                <div class="progress-bar-fill" style="width: <?= $pct ?>%; background: <?= $barColor ?>;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- MODULE: Cases by Status -->
    <div class="analytics-card">
      <h4><span><i class="fa-solid fa-chart-pie" style="margin-right: 8px; color: #333333;"></i> Cases by Status</span></h4>
      <div style="margin-top: 15px;">
        <?php 
        $statuses = [
            ['name' => 'Pending', 'count' => $stats['pending_cases'], 'icon' => 'fa-clock', 'color' => '#D76900'],
            ['name' => 'New', 'count' => $stats['new_cases'], 'icon' => 'fa-bell', 'color' => '#008fd2'],
            ['name' => 'Closed', 'count' => $stats['closed_cases'], 'icon' => 'fa-square-check', 'color' => '#009966']
        ];
        foreach ($statuses as $st):
            $pct = round(($st['count'] / $safeTotal) * 100, 1);
        ?>
          <div class="breakdown-row">
            <div class="breakdown-info">
              <span><i class="fa-solid <?= $st['icon'] ?>" style="color: <?= $st['color'] ?>;"></i> <?= $st['name'] ?></span>
              <span><strong><?= $st['count'] ?></strong> <span style="color:#777; font-weight:normal;">(<?= $pct ?>%)</span></span>
            </div>
            <div class="progress-track">
              <div class="progress-bar-fill" style="width: <?= $pct ?>%; background: <?= $st['color'] ?>;"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- SUBMISSION TREND LINE CHART -->
    <div class="analytics-card full-width">
      <h4><span><i class="fa-solid fa-chart-line" style="margin-right: 8px; color: #E81A3B;"></i> Submission Volume Trend</span></h4>
      <canvas id="casesTrendChart" style="max-height: 220px;"></canvas>
    </div>
  </div>

  <!-- MODULE 1: REROUTED CASES (SENT TO PARTNER) -->
  <div class="analytics-card full-width" style="margin-bottom:30px;">
    <h4>
      <span><i class="fa-solid fa-share-nodes" style="margin-right: 8px; color: #E81A3B;"></i> Cases Rerouted (Sent to Partner)</span>
      <span class="badge badge-danger"><?= $stats['rerouted_count'] ?> Total Escalated</span>
    </h4>
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Case Ref #</th>
            <th>Submission Date</th>
            <th>Updated By</th>
            <th>Relevance Resolution Note</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reroutedCases)): ?>
            <tr><td colspan="6" style="text-align:center; color:#999; padding:20px;">No rerouted cases found for this selected period.</td></tr>
          <?php else: ?>
            <?php foreach ($reroutedCases as $case): 
              $caseRef = $case['casenumber'] ?? $case['case_number'] ?? 'N/A';
            ?>
              <tr>
                <td><strong><?= htmlspecialchars($caseRef) ?></strong></td>
                <td><?= date('Y-m-d H:i', strtotime($case['submitted_at'])) ?></td>
                <td><?= htmlspecialchars($case['updated_by'] ?? 'System Admin') ?></td>
                <td><?= htmlspecialchars('Case identified as Core Banking Operation Issue and routed to BK Support Team') ?></td>
                <td><span class="badge badge-success"><?= htmlspecialchars($case['status'] ?? 'Closed') ?></span></td>
                <td><a href="../cases/view.php?casenumber=<?= urlencode($caseRef) ?>" style="color:#E81A3B; font-weight:600;"><i class="fa-solid fa-arrow-up-right-from-square"></i> View</a></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Submission Trend Line Chart
const trendLabels = <?= json_encode(array_keys($trendRaw)) ?>;
const trendValues = <?= json_encode(array_values($trendRaw)) ?>;

if (document.getElementById('casesTrendChart') && trendLabels.length > 0) {
  new Chart(document.getElementById('casesTrendChart'), {
    type: 'line',
  data: {
      labels: trendLabels,
    datasets: [{
      label: 'Cases Submitted',
        data: trendValues,
      borderColor: '#E81A3B',
      backgroundColor: 'rgba(232, 26, 59, 0.08)',
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } },
    plugins: { legend: { display: false } }
  }
});
}
</script>