<!-- FILE: /app/views/admin/index.php -->
<div class="page-header">
    <h1>Platform Admin Dashboard</h1>
</div>

<div class="admin-stats">
    <div class="stat-card">
        <h3>Total Tenants</h3>
        <p class="stat-value"><?php echo count($tenants); ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Users</h3>
        <p class="stat-value"><?php echo number_format($totalUsers); ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Jobs</h3>
        <p class="stat-value"><?php echo number_format($totalJobs); ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Avatars</h3>
        <p class="stat-value"><?php echo number_format($totalAvatars); ?></p>
    </div>
</div>

<div class="admin-section">
    <h2>Quick Actions</h2>
    <div class="action-buttons">
        <a href="/admin/tenants" class="btn btn-primary">Manage Tenants</a>
        <a href="/admin/styles" class="btn btn-primary">Manage Global Styles</a>
    </div>
</div>

<div class="admin-section">
    <h2>Recent Tenants</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_slice($tenants, 0, 10) as $tenant): ?>
                <tr>
                    <td><?php echo $tenant['id']; ?></td>
                    <td><?php echo htmlspecialchars($tenant['name']); ?></td>
                    <td><?php echo htmlspecialchars($tenant['slug']); ?></td>
                    <td><span class="badge badge-<?php echo $tenant['status']; ?>"><?php echo htmlspecialchars($tenant['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($tenant['created_at'])); ?></td>
                    <td>
                        <?php if ($tenant['status'] === 'active'): ?>
                            <form action="/admin/disable-tenant/<?php echo $tenant['id']; ?>" method="POST" style="display:inline;">
                                <?php echo CSRF::field(); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Disable</button>
                            </form>
                        <?php else: ?>
                            <form action="/admin/enable-tenant/<?php echo $tenant['id']; ?>" method="POST" style="display:inline;">
                                <?php echo CSRF::field(); ?>
                                <button type="submit" class="btn btn-sm btn-success">Enable</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
