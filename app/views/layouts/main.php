<!-- FILE: /app/views/layouts/main.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'SplashAvatar'); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="/dashboard">SplashAvatar</a>
            </div>
            <ul class="navbar-menu">
                <?php if (Auth::check()): ?>
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a href="/faces">Faces</a></li>
                    <li><a href="/styles">Styles</a></li>
                    <li><a href="/jobs">Jobs</a></li>
                    <li><a href="/packs">Packs</a></li>
                    <?php if (Auth::hasAnyRole(['tenant_admin', 'platform_admin'])): ?>
                        <li><a href="/branding">Branding</a></li>
                        <li><a href="/billing">Billing</a></li>
                    <?php endif; ?>
                    <?php if (Auth::isPlatformAdmin()): ?>
                        <li><a href="/admin">Admin</a></li>
                    <?php endif; ?>
                    <li class="user-menu">
                        <span><?php echo htmlspecialchars(Auth::user()['name']); ?></span>
                        <a href="/logout">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container main-content">
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

        <?php if (Session::hasFlash('info')): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars(Session::flash('info')); ?>
            </div>
        <?php endif; ?>

        <?php echo $content; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SplashAvatar - AI Avatar Generator SaaS</p>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
