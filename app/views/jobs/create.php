<!-- FILE: /app/views/jobs/create.php -->
<div class="page-header">
    <h1>Create Avatar Job</h1>
</div>

<div class="credits-info">
    <p><strong>Available Credits:</strong> <?php echo number_format($credits['credits_remaining']); ?></p>
</div>

<form action="/jobs/create" method="POST" class="job-form" id="jobForm">
    <?php echo CSRF::field(); ?>

    <div class="form-group">
        <label for="title">Job Title</label>
        <input type="text" id="title" name="title" required class="form-control" value="Avatar Job - <?php echo date('Y-m-d H:i'); ?>">
    </div>

    <div class="form-group">
        <label for="description">Description (Optional)</label>
        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label>Select Face Photos</label>
        <?php if (!empty($faces)): ?>
            <div class="face-select-grid">
                <?php foreach ($faces as $face): ?>
                    <label class="face-select-item">
                        <input type="checkbox" name="face_ids[]" value="<?php echo $face['id']; ?>">
                        <img src="/storage/uploads/<?php echo htmlspecialchars($face['file_path']); ?>" alt="<?php echo htmlspecialchars($face['title']); ?>">
                        <span><?php echo htmlspecialchars($face['title']); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-error">No validated face photos available. <a href="/faces/upload">Upload photos first</a></p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>Select Avatar Styles</label>
        <?php if (!empty($styles)): ?>
            <div class="style-select-grid">
                <?php foreach ($styles as $style): ?>
                    <label class="style-select-item">
                        <input type="checkbox" name="style_ids[]" value="<?php echo $style['id']; ?>" data-credits="<?php echo $style['credits_cost']; ?>">
                        <div class="style-preview-small"><?php echo htmlspecialchars(substr($style['name'], 0, 2)); ?></div>
                        <span><?php echo htmlspecialchars($style['name']); ?></span>
                        <span class="credits-badge"><?php echo $style['credits_cost']; ?> credits</span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-error">No styles available.</p>
        <?php endif; ?>
    </div>

    <div class="form-summary">
        <p><strong>Estimated Credits Cost:</strong> <span id="totalCredits">0</span></p>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary" <?php echo (empty($faces) || empty($styles)) ? 'disabled' : ''; ?>>Create Job</button>
        <a href="/jobs" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faceCheckboxes = document.querySelectorAll('input[name="face_ids[]"]');
    const styleCheckboxes = document.querySelectorAll('input[name="style_ids[]"]');
    const totalCreditsSpan = document.getElementById('totalCredits');

    function calculateTotal() {
        const faceCount = Array.from(faceCheckboxes).filter(cb => cb.checked).length;
        let totalCredits = 0;
        styleCheckboxes.forEach(cb => {
            if (cb.checked) {
                totalCredits += parseInt(cb.dataset.credits);
            }
        });
        totalCreditsSpan.textContent = totalCredits * faceCount;
    }

    faceCheckboxes.forEach(cb => cb.addEventListener('change', calculateTotal));
    styleCheckboxes.forEach(cb => cb.addEventListener('change', calculateTotal));
});
</script>
