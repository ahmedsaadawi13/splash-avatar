<!-- FILE: /app/views/dashboard/index.php -->
<div class="dashboard">
    <h1>Dashboard</h1>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Credits Remaining</h3>
            <div class="stat-value"><?php echo number_format($credits['credits_remaining']); ?></div>
            <p class="stat-label">of <?php echo number_format($credits['credits_allocated']); ?> allocated</p>
        </div>

        <div class="stat-card">
            <h3>Total Jobs</h3>
            <div class="stat-value"><?php echo number_format($stats['total_jobs']); ?></div>
            <p class="stat-label">this month</p>
        </div>

        <div class="stat-card">
            <h3>Avatars Generated</h3>
            <div class="stat-value"><?php echo number_format($stats['total_avatars']); ?></div>
            <p class="stat-label">this month</p>
        </div>

        <div class="stat-card">
            <h3>Storage Used</h3>
            <div class="stat-value"><?php echo $stats['storage_mb']; ?> MB</div>
            <p class="stat-label">of <?php echo $subscription['max_storage_mb'] ?? 'unlimited'; ?> MB</p>
        </div>
    </div>

    <div class="dashboard-section">
        <h2>Current Plan</h2>
        <div class="plan-info">
            <p><strong><?php echo htmlspecialchars($subscription['plan_name'] ?? 'No active plan'); ?></strong></p>
            <p>Monthly Credits: <?php echo number_format($subscription['monthly_credits'] ?? 0); ?></p>
            <p>Status: <span class="badge badge-<?php echo $subscription['status'] ?? 'inactive'; ?>"><?php echo htmlspecialchars($subscription['status'] ?? 'inactive'); ?></span></p>
        </div>
    </div>

    <div class="dashboard-section">
        <h2>Recent Jobs</h2>
        <?php if (!empty($recentJobs)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Avatars</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentJobs as $job): ?>
                        <tr>
                            <td><?php echo $job['id']; ?></td>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><span class="badge badge-<?php echo $job['status']; ?>"><?php echo htmlspecialchars($job['status']); ?></span></td>
                            <td><?php echo $job['total_avatars_generated']; ?> / <?php echo $job['total_avatars_requested']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                            <td><a href="/jobs/view/<?php echo $job['id']; ?>" class="btn btn-sm">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No jobs yet. <a href="/jobs/create">Create your first job</a></p>
        <?php endif; ?>
    </div>

    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="/faces/upload" class="btn btn-primary">Upload Faces</a>
            <a href="/jobs/create" class="btn btn-primary">Create Job</a>
            <a href="/styles" class="btn btn-secondary">Browse Styles</a>
        </div>
    </div>
</div>
