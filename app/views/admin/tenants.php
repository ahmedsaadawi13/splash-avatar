<!-- FILE: /app/views/admin/tenants.php -->
<div class="page-header">
    <h1>Manage Tenants</h1>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Status</th>
            <th>Created</th>
            <th>Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tenants as $tenant): ?>
            <tr>
                <td><?php echo $tenant['id']; ?></td>
                <td><?php echo htmlspecialchars($tenant['name']); ?></td>
                <td><?php echo htmlspecialchars($tenant['slug']); ?></td>
                <td><span class="badge badge-<?php echo $tenant['status']; ?>"><?php echo htmlspecialchars($tenant['status']); ?></span></td>
                <td><?php echo date('M d, Y', strtotime($tenant['created_at'])); ?></td>
                <td><?php echo date('M d, Y', strtotime($tenant['updated_at'])); ?></td>
                <td>
                    <?php if ($tenant['status'] === 'active'): ?>
                        <form action="/admin/disable-tenant/<?php echo $tenant['id']; ?>" method="POST" style="display:inline;">
                            <?php echo CSRF::field(); ?>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Disable this tenant?')">Disable</button>
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
