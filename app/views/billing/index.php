<!-- FILE: /app/views/billing/index.php -->
<div class="page-header">
    <h1>Billing & Usage</h1>
</div>

<div class="billing-section">
    <h2>Current Plan</h2>
    <div class="plan-card">
        <h3><?php echo htmlspecialchars($subscription['plan_name'] ?? 'No active plan'); ?></h3>
        <p>Monthly Credits: <?php echo number_format($subscription['monthly_credits'] ?? 0); ?></p>
        <p>Max Users: <?php echo $subscription['max_users'] ?? 'Unlimited'; ?></p>
        <p>Max Storage: <?php echo $subscription['max_storage_mb'] ?? 'Unlimited'; ?> MB</p>
        <p>Status: <span class="badge badge-<?php echo $subscription['status'] ?? 'inactive'; ?>"><?php echo htmlspecialchars($subscription['status'] ?? 'inactive'); ?></span></p>
    </div>
</div>

<div class="billing-section">
    <h2>Current Month Credits</h2>
    <div class="credits-card">
        <p><strong>Allocated:</strong> <?php echo number_format($credits['credits_allocated']); ?></p>
        <p><strong>Used:</strong> <?php echo number_format($credits['credits_used']); ?></p>
        <p><strong>Remaining:</strong> <?php echo number_format($credits['credits_remaining']); ?></p>
        <div class="credits-progress">
            <div class="progress-bar" style="width: <?php echo ($credits['credits_allocated'] > 0) ? (($credits['credits_used'] / $credits['credits_allocated']) * 100) : 0; ?>%"></div>
        </div>
    </div>
</div>

<div class="billing-section">
    <h2>Usage Stats</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <p>Total Jobs</p>
            <p class="stat-value"><?php echo number_format($stats['total_jobs']); ?></p>
        </div>
        <div class="stat-item">
            <p>Avatars Generated</p>
            <p class="stat-value"><?php echo number_format($stats['total_avatars']); ?></p>
        </div>
        <div class="stat-item">
            <p>Storage Used</p>
            <p class="stat-value"><?php echo $stats['storage_mb']; ?> MB</p>
        </div>
    </div>
</div>

<div class="billing-section" id="api-keys">
    <h2>API Keys</h2>
    <?php if (!empty($apiKeys)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>API Key</th>
                    <th>Status</th>
                    <th>Rate Limit</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apiKeys as $key): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($key['label']); ?></td>
                        <td><code><?php echo htmlspecialchars(substr($key['api_key'], 0, 20)); ?>...</code></td>
                        <td><span class="badge badge-<?php echo $key['is_active'] ? 'active' : 'inactive'; ?>"><?php echo $key['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                        <td><?php echo $key['rate_limit_per_minute']; ?>/min</td>
                        <td><?php echo date('M d, Y', strtotime($key['created_at'])); ?></td>
                        <td>
                            <?php if ($key['is_active']): ?>
                                <form action="/billing/revoke-key/<?php echo $key['id']; ?>" method="POST" style="display:inline;">
                                    <?php echo CSRF::field(); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Revoke this API key?')">Revoke</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No API keys generated yet.</p>
    <?php endif; ?>

    <form action="/billing/generate-api-key" method="POST" class="api-key-form">
        <?php echo CSRF::field(); ?>
        <div class="form-inline">
            <input type="text" name="label" placeholder="API Key Label" class="form-control" required>
            <button type="submit" class="btn btn-primary">Generate New API Key</button>
        </div>
    </form>
</div>
