<!-- FILE: /app/views/layouts/auth.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'SplashAvatar'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h1>SplashAvatar</h1>
            <p>AI Avatar Generator SaaS</p>
        </div>

        <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars(Session::flash('success')); ?>
            </div>
        <?php endif; ?>

        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars(Session::flash('error')); ?>
            </div>
        <?php endif; ?>

        <?php echo $content; ?>

        <div class="auth-footer">
            <p>&copy; <?php echo date('Y'); ?> SplashAvatar</p>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
