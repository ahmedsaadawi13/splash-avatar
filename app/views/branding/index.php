<!-- FILE: /app/views/branding/index.php -->
<div class="page-header">
    <h1>Branding Settings</h1>
</div>

<form action="/branding/update" method="POST" enctype="multipart/form-data" class="branding-form">
    <?php echo CSRF::field(); ?>

    <div class="form-group">
        <label for="brand_name">Brand Name</label>
        <input type="text" id="brand_name" name="brand_name" value="<?php echo htmlspecialchars($branding['brand_name'] ?? ''); ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="logo">Logo</label>
        <?php if ($branding && $branding['logo_path']): ?>
            <div class="current-logo">
                <img src="/storage/uploads/<?php echo htmlspecialchars($branding['logo_path']); ?>" alt="Logo" style="max-width: 200px;">
            </div>
        <?php endif; ?>
        <input type="file" id="logo" name="logo" accept="image/*" class="form-control">
    </div>

    <div class="form-group">
        <label for="primary_color">Primary Color</label>
        <input type="color" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars($branding['primary_color'] ?? '#007bff'); ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="secondary_color">Secondary Color</label>
        <input type="color" id="secondary_color" name="secondary_color" value="<?php echo htmlspecialchars($branding['secondary_color'] ?? '#6c757d'); ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="watermark_text">Watermark Text</label>
        <input type="text" id="watermark_text" name="watermark_text" value="<?php echo htmlspecialchars($branding['watermark_text'] ?? ''); ?>" class="form-control">
    </div>

    <div class="form-group">
        <label for="watermark_opacity">Watermark Opacity (0-100)</label>
        <input type="number" id="watermark_opacity" name="watermark_opacity" value="<?php echo htmlspecialchars($branding['watermark_opacity'] ?? '50'); ?>" min="0" max="100" class="form-control">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Branding</button>
    </div>
</form>
