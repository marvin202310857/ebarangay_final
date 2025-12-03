<?php
// admin/reports.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get statistics
$total_residents = $conn->query("SELECT COUNT(*) as c FROM residents")->fetch_assoc()['c'];
$total_households = $conn->query("SELECT COUNT(*) as c FROM households")->fetch_assoc()['c'];
$total_clearances = $conn->query("SELECT COUNT(*) as c FROM clearances")->fetch_assoc()['c'];
$total_blotters = $conn->query("SELECT COUNT(*) as c FROM blotter_reports")->fetch_assoc()['c'];
$total_transactions = $conn->query("SELECT COUNT(*) as c FROM transactions")->fetch_assoc()['c'];

// Gender distribution
$male = $conn->query("SELECT COUNT(*) as c FROM residents WHERE gender='Male'")->fetch_assoc()['c'];
$female = $conn->query("SELECT COUNT(*) as c FROM residents WHERE gender='Female'")->fetch_assoc()['c'];

// Age groups
$age_0_17 = $conn->query("SELECT COUNT(*) as c FROM residents WHERE TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) < 18")->fetch_assoc()['c'];
$age_18_59 = $conn->query("SELECT COUNT(*) as c FROM residents WHERE TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 18 AND 59")->fetch_assoc()['c'];
$age_60_plus = $conn->query("SELECT COUNT(*) as c FROM residents WHERE TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60")->fetch_assoc()['c'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-graph-up"></i> Reports & Analytics</h1>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Report
        </button>
    </div>

    <!-- Summary Cards -->
    <h5 class="mb-3">Population Statistics</h5>
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Residents</h6>
                    <h2><?php echo $total_residents; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Total Households</h6>
                    <h2><?php echo $total_households; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Male</h6>
                    <h2><?php echo $male; ?></h2>
                    <small><?php echo $total_residents > 0 ? round(($male / $total_residents) * 100, 1) : 0; ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Female</h6>
                    <h2><?php echo $female; ?></h2>
                    <small><?php echo $total_residents > 0 ? round(($female / $total_residents) * 100, 1) : 0; ?>%</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Age Distribution -->
    <h5 class="mb-3">Age Distribution</h5>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Children (0-17)</h6>
                    <h3><?php echo $age_0_17; ?></h3>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" style="width: <?php echo $total_residents > 0 ? ($age_0_17 / $total_residents) * 100 : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Adults (18-59)</h6>
                    <h3><?php echo $age_18_59; ?></h3>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $total_residents > 0 ? ($age_18_59 / $total_residents) * 100 : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Senior Citizens (60+)</h6>
                    <h3><?php echo $age_60_plus; ?></h3>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning" style="width: <?php echo $total_residents > 0 ? ($age_60_plus / $total_residents) * 100 : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Statistics -->
    <h5 class="mb-3">Services Statistics</h5>
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted"><i class="bi bi-file-earmark-check"></i> Clearances Issued</h6>
                    <h2><?php echo $total_clearances; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted"><i class="bi bi-journal-medical"></i> Blotter Reports</h6>
                    <h2><?php echo $total_blotters; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted"><i class="bi bi-file-text"></i> Total Transactions</h6>
                    <h2><?php echo $total_transactions; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Placeholders -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Monthly Clearances Trend</strong>
                </div>
                <div class="card-body">
                    <canvas id="clearancesChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Civil Status Distribution</strong>
                </div>
                <div class="card-body">
                    <canvas id="civilStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sample chart - you can customize with real data
const ctx1 = document.getElementById('clearancesChart');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Clearances',
            data: [12, 19, 15, 25, 22, 30],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
});

const ctx2 = document.getElementById('civilStatusChart');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Single', 'Married', 'Widowed', 'Separated'],
        datasets: [{
            data: [45, 35, 15, 5],
            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
        }]
    }
});
</script>

<?php include 'includes/footer.php'; ?>
