<!-- FILE: /app/views/faces/index.php -->
<div class="page-header">
    <h1>Face Uploads</h1>
    <a href="/faces/upload" class="btn btn-primary">Upload New Photos</a>
</div>

<?php if (!empty($uploads)): ?>
    <div class="face-grid">
        <?php foreach ($uploads as $upload): ?>
            <div class="face-card">
                <div class="face-image">
                    <img src="/storage/uploads/<?php echo htmlspecialchars($upload['file_path']); ?>" alt="<?php echo htmlspecialchars($upload['title']); ?>">
                </div>
                <div class="face-info">
                    <h3><?php echo htmlspecialchars($upload['title']); ?></h3>
                    <p class="face-status">
                        <span class="badge badge-<?php echo $upload['status']; ?>"><?php echo htmlspecialchars($upload['status']); ?></span>
                        <?php if ($upload['face_detected']): ?>
                            <span class="badge badge-success">Face Detected</span>
                        <?php endif; ?>
                    </p>
                    <?php if ($upload['rejection_reason']): ?>
                        <p class="text-error"><?php echo htmlspecialchars($upload['rejection_reason']); ?></p>
                    <?php endif; ?>
                    <p class="face-meta">
                        Uploaded: <?php echo date('M d, Y', strtotime($upload['created_at'])); ?>
                    </p>
                    <form action="/faces/delete/<?php echo $upload['id']; ?>" method="POST" style="display:inline;">
                        <?php echo CSRF::field(); ?>
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this upload?')">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <p>No face photos uploaded yet.</p>
        <a href="/faces/upload" class="btn btn-primary">Upload Your First Photos</a>
    </div>
<?php endif; ?>
