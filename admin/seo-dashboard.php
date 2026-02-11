<?php
// Admin SEO Dashboard
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/classes/ContentRefreshEngine.php';

// Auth Check (Mock - adapt to actual admin auth)
if (!isset($_SESSION['admin_logged_in'])) {
    // header('Location: login.php'); // Uncomment in prod
    // exit;
}

$refreshEngine = new ContentRefreshEngine();
$staleContent = $refreshEngine->getStaleContent();

$pageTitle = "SEO Refresh Engine";
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Content Refresh Engine</h1>
            <p class="mb-4">AI-driven insights on content freshness and optimization needs.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pages Needing Refresh</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo count($staleContent); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Stale Content Report</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Page / Package</th>
                            <th>Type</th>
                            <th>Freshness Score</th>
                            <th>Issues Detected</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($staleContent)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Great job! All content is fresh.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($staleContent as $item): ?>
                                <tr>
                                    <td class="font-weight-bold">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </td>
                                    <td><span class="badge badge-secondary">
                                            <?php echo $item['type']; ?>
                                        </span></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-<?php echo $item['score'] < 40 ? 'danger' : 'warning'; ?>"
                                                role="progressbar" style="width: <?php echo $item['score']; ?>%"
                                                aria-valuenow="<?php echo $item['score']; ?>" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <?php echo $item['score']; ?>/100
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-danger small">
                                        <?php echo htmlspecialchars($item['reason']); ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary shadow-sm">
                                            <i class="fas fa-edit fa-sm text-white-50"></i> Optimize
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>