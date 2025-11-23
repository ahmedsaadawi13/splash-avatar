<!-- FILE: /app/views/packs/view.php -->
<div class="page-header">
    <h1><?php echo htmlspecialchars($pack['name']); ?></h1>
    <a href="/packs/download-zip/<?php echo $pack['id']; ?>" class="btn btn-primary">Download All (ZIP)</a>
</div>

<div class="pack-info">
    <p><strong>Description:</strong> <?php echo htmlspecialchars($pack['description']); ?></p>
    <p><strong>Total Avatars:</strong> <?php echo $pack['total_avatars']; ?></p>
    <p><strong>Created:</strong> <?php echo date('M d, Y H:i', strtotime($pack['created_at'])); ?></p>
</div>

<?php if (!empty($pack['avatars'])): ?>
    <div class="avatar-gallery">
        <?php foreach ($pack['avatars'] as $avatar): ?>
            <div class="avatar-item">
                <div class="avatar-image">
                    <img src="/storage/uploads/<?php echo htmlspecialchars($avatar['image_path']); ?>" alt="Avatar">
                </div>
                <div class="avatar-meta">
                    <p><?php echo htmlspecialchars($avatar['style_name']); ?></p>
                    <p class="avatar-dimensions"><?php echo $avatar['width']; ?>x<?php echo $avatar['height']; ?>px</p>
                    <p class="download-count">Downloads: <?php echo $avatar['download_count']; ?></p>
                    <a href="/packs/download/<?php echo $avatar['id']; ?>" class="btn btn-sm btn-primary">Download</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No avatars in this pack.</p>
<?php endif; ?>
