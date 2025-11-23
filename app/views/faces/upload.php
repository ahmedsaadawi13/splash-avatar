<!-- FILE: /app/views/faces/upload.php -->
<div class="page-header">
    <h1>Upload Face Photos</h1>
</div>

<div class="upload-container">
    <div class="upload-instructions">
        <h3>Upload Guidelines</h3>
        <ul>
            <li>Accepted formats: JPG, JPEG, PNG</li>
            <li>Maximum file size: 10 MB per photo</li>
            <li>Photos should clearly show faces</li>
            <li>You can upload up to 5 photos at once</li>
        </ul>
    </div>

    <form action="/faces/upload" method="POST" enctype="multipart/form-data" class="upload-form">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="face_photos">Select Photos</label>
            <input type="file" id="face_photos" name="face_photos[]" multiple accept="image/jpeg,image/jpg,image/png" required class="form-control">
            <p class="help-text">Hold Ctrl/Cmd to select multiple files</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Upload Photos</button>
            <a href="/faces" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
