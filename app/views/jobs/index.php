<!-- FILE: /app/views/jobs/index.php -->
<div class="page-header">
    <h1>Avatar Jobs</h1>
    <a href="/jobs/create" class="btn btn-primary">Create New Job</a>
</div>

<?php if (!empty($jobs)): ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Credits Cost</th>
                <th>Avatars</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?php echo $job['id']; ?></td>
                    <td><?php echo htmlspecialchars($job['title']); ?></td>
                    <td><span class="badge badge-<?php echo $job['status']; ?>"><?php echo htmlspecialchars($job['status']); ?></span></td>
                    <td><?php echo $job['credits_cost']; ?></td>
                    <td><?php echo $job['total_avatars_generated']; ?> / <?php echo $job['total_avatars_requested']; ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($job['created_at'])); ?></td>
                    <td>
                        <a href="/jobs/view/<?php echo $job['id']; ?>" class="btn btn-sm">View</a>
                        <?php if ($job['status'] === 'pending'): ?>
                            <a href="/jobs/run/<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">Run</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="empty-state">
        <p>No jobs created yet.</p>
        <a href="/jobs/create" class="btn btn-primary">Create Your First Job</a>
    </div>
<?php endif; ?>
