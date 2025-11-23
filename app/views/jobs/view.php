<!-- FILE: /app/views/jobs/view.php -->
<div class="page-header">
    <h1><?php echo htmlspecialchars($job['title']); ?></h1>
    <div class="job-status">
        <span class="badge badge-<?php echo $job['status']; ?> badge-lg"><?php echo htmlspecialchars($job['status']); ?></span>
    </div>
</div>

<div class="job-details">
    <div class="detail-section">
        <h3>Job Information</h3>
        <p><strong>Job ID:</strong> <?php echo $job['id']; ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
        <p><strong>Credits Cost:</strong> <?php echo $job['credits_cost']; ?></p>
        <p><strong>Avatars Requested:</strong> <?php echo $job['total_avatars_requested']; ?></p>
        <p><strong>Avatars Generated:</strong> <?php echo $job['total_avatars_generated']; ?></p>
        <p><strong>Created:</strong> <?php echo date('M d, Y H:i', strtotime($job['created_at'])); ?></p>
        <?php if ($job['error_message']): ?>
            <p class="text-error"><strong>Error:</strong> <?php echo htmlspecialchars($job['error_message']); ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($job['faces'])): ?>
        <div class="detail-section">
            <h3>Source Faces (<?php echo count($job['faces']); ?>)</h3>
            <div class="face-preview-grid">
                <?php foreach ($job['faces'] as $face): ?>
                    <img src="/storage/uploads/<?php echo htmlspecialchars($face['file_path']); ?>" alt="Face" class="face-preview-thumb">
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($job['styles'])): ?>
        <div class="detail-section">
            <h3>Selected Styles (<?php echo count($job['styles']); ?>)</h3>
            <ul>
                <?php foreach ($job['styles'] as $style): ?>
                    <li><?php echo htmlspecialchars($style['name']); ?> (<?php echo $style['credits_cost']; ?> credits)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<?php if ($job['status'] === 'completed' && !empty($avatars)): ?>
    <div class="avatars-section">
        <h2>Generated Avatars (<?php echo count($avatars); ?>)</h2>
        <div class="avatar-gallery">
            <?php foreach ($avatars as $avatar): ?>
                <div class="avatar-item">
                    <img src="/storage/uploads/<?php echo htmlspecialchars($avatar['image_path']); ?>" alt="Avatar">
                    <div class="avatar-meta">
                        <p><?php echo htmlspecialchars($avatar['style_name']); ?></p>
                        <a href="/packs/download/<?php echo $avatar['id']; ?>" class="btn btn-sm btn-primary">Download</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($pack): ?>
            <div class="pack-link">
                <a href="/packs/<?php echo $pack['id']; ?>" class="btn btn-primary">View Avatar Pack</a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($job['status'] === 'pending'): ?>
    <div class="job-actions">
        <a href="/jobs/run/<?php echo $job['id']; ?>" class="btn btn-primary btn-lg">Run Job Now</a>
    </div>
<?php endif; ?>
