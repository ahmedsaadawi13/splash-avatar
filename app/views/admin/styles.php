<!-- FILE: /app/views/admin/styles.php -->
<div class="page-header">
    <h1>Manage Global Styles</h1>
</div>

<div class="create-style-form">
    <h2>Create New Global Style</h2>
    <form action="/admin/create-style" method="POST">
        <?php echo CSRF::field(); ?>
        <div class="form-inline">
            <input type="text" name="name" placeholder="Style Name" required class="form-control">
            <input type="text" name="description" placeholder="Description" class="form-control">
            <input type="number" name="credits_cost" placeholder="Credits Cost" value="10" min="1" class="form-control">
            <button type="submit" class="btn btn-primary">Create Style</button>
        </div>
    </form>
</div>

<div class="styles-list">
    <h2>Existing Global Styles</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Credits Cost</th>
                <th>Difficulty</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($styles as $style): ?>
                <tr>
                    <td><?php echo $style['id']; ?></td>
                    <td><?php echo htmlspecialchars($style['name']); ?></td>
                    <td><?php echo htmlspecialchars($style['slug']); ?></td>
                    <td><?php echo htmlspecialchars($style['description']); ?></td>
                    <td><?php echo $style['credits_cost']; ?></td>
                    <td><?php echo htmlspecialchars($style['difficulty_level']); ?></td>
                    <td><span class="badge badge-<?php echo $style['is_active'] ? 'active' : 'inactive'; ?>"><?php echo $style['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($style['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
