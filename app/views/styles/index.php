<!-- FILE: /app/views/styles/index.php -->
<div class="page-header">
    <h1>Avatar Styles</h1>
</div>

<?php if (!empty($styles)): ?>
    <div class="styles-grid">
        <?php foreach ($styles as $style): ?>
            <div class="style-card">
                <div class="style-preview">
                    <?php if ($style['preview_image_path']): ?>
                        <img src="/storage/uploads/<?php echo htmlspecialchars($style['preview_image_path']); ?>" alt="<?php echo htmlspecialchars($style['name']); ?>">
                    <?php else: ?>
                        <div class="style-placeholder"><?php echo strtoupper(substr($style['name'], 0, 2)); ?></div>
                    <?php endif; ?>
                </div>
                <div class="style-info">
                    <h3><?php echo htmlspecialchars($style['name']); ?></h3>
                    <p><?php echo htmlspecialchars($style['description']); ?></p>
                    <div class="style-meta">
                        <span class="badge"><?php echo htmlspecialchars($style['difficulty_level']); ?></span>
                        <span class="credits-cost"><?php echo $style['credits_cost']; ?> credits</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <p>No styles available.</p>
    </div>
<?php endif; ?>
