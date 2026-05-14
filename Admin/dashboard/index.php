<?php
require '../auth/auth_check.php';
$pageTitle = 'Dashboard';
require '../includes/header.php';
require '../includes/sidebar.php';

require '../../config/db.php';

$stats = [];

$stats['total_cases'] = $pdo->query(
    "SELECT COUNT(*) FROM cases"
)->fetchColumn();

$stats['pending_cases'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE status='Pending'"
)->fetchColumn();

$stats['in_progress_cases'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE status='new'"
)->fetchColumn();

$stats['closed_cases'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE status='Closed'"
)->fetchColumn();

$stats['low_sensitivity'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE Case_sensitivity = 'Low'"
)->fetchColumn();

$stats['medium_sensitivity'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE Case_sensitivity = 'Medium'"
)->fetchColumn();

$stats['high_sensitivity'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE Case_sensitivity = 'High'"
)->fetchColumn();

$stats['critical_sensitivity'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE Case_sensitivity = 'Critical'"
)->fetchColumn();
$stats['relevant_cases'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE Case_relevance = 'Relevant'"
)->fetchColumn();

$stats['irrelevant_cases'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE Case_relevance = 'Irrelevant'"
)->fetchColumn();

$stats['core_banking_cases'] = $pdo->query(
    "SELECT COUNT(*) FROM cases WHERE Case_relevance = 'Core Banking'"
)->fetchColumn();


$stats['total_clients'] = $pdo->query(
    "SELECT COUNT(*) FROM clients"
)->fetchColumn();

$stats['total_users'] = $pdo->query(
    "SELECT COUNT(*) FROM admin_users"
)->fetchColumn();

// Trends
//Trend: cases created per day (last 30 days)
$trendStmt = $pdo->prepare("
  SELECT 
    DATE(submitted_at) AS day,
    COUNT(*) AS total
  FROM cases
  WHERE submitted_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
  GROUP BY DATE(submitted_at)
  ORDER BY day ASC
");
$trendStmt->execute();
$trendRaw = $trendStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Build full date range (last 30 days)
$trendData = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $trendData[$date] = 0;
}

// ✅ Fill actual data
foreach ($trendRaw as $row) {
    $trendData[$row['day']] = (int)$row['total'];
}

// ✅ Separate labels & values (for Chart.js)
$trendLabels = array_keys($trendData);
$trendValues = array_values($trendData);



?>

<h1>Dashboard</h1>
<p class="muted">Overview of system activity</p>

<div class="stats-grid">
  <div class="stat-card">
    <span class="label">Total Cases</span>
    <span class="value"><?= $stats['total_cases'] ?></span>
  </div>
 
  <a href="../cases/index.php?search=&date_from=&date_to=&status=Pending&sensitivity=&identity_choice=" class="dashboard-card-link">
    <div class="stat-card warning">
      <span class="label">Pending Cases</span>
      <span class="value"><?= $stats['pending_cases'] ?></span>
    </div>
  </a>
  <a href="../cases/index.php?search=&date_from=&date_to=&status=New&sensitivity=&identity_choice=" class="dashboard-card-link">
    <div class="stat-card info">
      <span class="label">New</span>
      <span class="value"><?= $stats['in_progress_cases'] ?></span>
    </div>
  </a>
  
  <a href="../cases/index.php?search=&date_from=&date_to=&status=Closed&sensitivity=&identity_choice=" class="dashboard-card-link">
    <div class="stat-card success">
      <span class="label">Closed Cases</span>
      <span class="value"><?= $stats['closed_cases'] ?></span>
    </div>
  </a>
<!--  -->
  <!-- <div class="stat-card">
    <span class="label">Clients</span>
    <span class="value"><?= $stats['total_clients'] ?></span>
  </div> -->

  <!-- <div class="stat-card">
    <span class="label">Admin Users</span>
    <span class="value"><?= $stats['total_users'] ?></span>
  </div> -->
</div>

<div class="dashboard-charts">

  <div class="chart-card">
    <h4>Cases by Status</h4>
    <canvas id="statusChart"></canvas>
  </div>

  <div class="chart-card">
    <h4>Cases by Sensitivity</h4>
    <canvas id="sensitivityChart"></canvas>
  </div>

  <div class="chart-card">
    <h4>Cases by Relevance</h4>
    <canvas id="relevanceChart"></canvas>
  </div>

  <div class="chart-card full-width">
    <h4>Cases Trend – Last 30 Days</h4>
    <canvas id="casesTrendChart"></canvas>
  </div>

</div>
<?php INCLUDE '../includes/footer.php'; ?>

<script>
new Chart(document.getElementById('statusChart'), {
  type: 'doughnut',
  data: {
    labels: ['Pending', 'Closed', 'New'],
    datasets: [{
      data: [<?= $stats['pending_cases'] ?>, <?= $stats['closed_cases'] ?>, <?= $stats['in_progress_cases'] ?>],
      backgroundColor: ['#D76900', '#009966', '#008fd2']
    }]
  },
  options: {
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});
</script>
<script>
new Chart(document.getElementById('sensitivityChart'), {
  type: 'bar',
  data: {
    labels: ['Low', 'Medium', 'High'],
    datasets: [{
      label: 'Cases',
      data: [
        <?= $stats['low_sensitivity'] ?>,
        <?= $stats['medium_sensitivity'] ?>,
        <?= $stats['high_sensitivity'] ?>
      ],
      backgroundColor: ['#5b6e7f', '#D76900', '#98002e']
    }]
  },
  options: {
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>
<script>
new Chart(document.getElementById('relevanceChart'), {
  type: 'pie',
  data: {
    labels: ['Relevant', 'Irrelevant', 'Core Banking Issues'],
    datasets: [{
      data: [
        <?= $stats['relevant_cases'] ?>,
        <?= $stats['irrelevant_cases'] ?>,
        <?= $stats['core_banking_cases'] ?>
      ],
      backgroundColor: ['#008fd2', '#E7E7E7', '#5b6e7f']
    }]
  },
  options: {
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});
</script>
<script>
const trendCtx = document.getElementById('casesTrendChart');

new Chart(trendCtx, {
  type: 'line',
  data: {
    labels: <?= json_encode($trendLabels) ?>,
    datasets: [{
      label: 'Cases Submitted',
      data: <?= json_encode($trendValues) ?>,
      borderColor: '#E81A3B',        // Brand red
      backgroundColor: 'rgba(232,26,59,0.1)',
      borderWidth: 2,
      tension: 0.3,
      fill: true,
      pointRadius: 3,
      pointHoverRadius: 6,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ctx.parsed.y + ' cases'
        }
      }
    },
    scales: {
      x: {
        ticks: { maxRotation: 0 },
        grid: { display: false }
      },
      y: {
        beginAtZero: true,
        grid: { color: '#E7E7E7' }
      }
    }
  }
});
</script>