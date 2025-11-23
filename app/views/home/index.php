<!-- FILE: /app/views/home/index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="landing-page">
    <header class="hero">
        <div class="container">
            <h1>SplashAvatar</h1>
            <p class="tagline">Transform Your Photos into Stunning AI Avatars</p>
            <p class="subtitle">Multi-tenant SaaS platform for generating professional avatar packs in multiple styles</p>
            <div class="cta-buttons">
                <a href="/register" class="btn btn-primary btn-lg">Start Free Trial</a>
                <a href="/login" class="btn btn-secondary btn-lg">Login</a>
            </div>
        </div>
    </header>

    <section class="features">
        <div class="container">
            <h2>Features</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <h3>Multiple Styles</h3>
                    <p>Choose from anime, 3D, cartoon, pixel art, cyberpunk, and more</p>
                </div>
                <div class="feature-card">
                    <h3>Batch Generation</h3>
                    <p>Upload multiple photos and generate avatar packs instantly</p>
                </div>
                <div class="feature-card">
                    <h3>Credits System</h3>
                    <p>Flexible pricing with monthly credits allocation</p>
                </div>
                <div class="feature-card">
                    <h3>REST API</h3>
                    <p>Programmatic access for apps and integrations</p>
                </div>
                <div class="feature-card">
                    <h3>Branding</h3>
                    <p>Custom watermarks and brand colors</p>
                </div>
                <div class="feature-card">
                    <h3>Multi-Tenant</h3>
                    <p>Perfect for agencies, apps, and influencers</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <a href="/register" class="btn btn-primary btn-lg">Create Free Account</a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SplashAvatar - AI Avatar Generator SaaS</p>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
