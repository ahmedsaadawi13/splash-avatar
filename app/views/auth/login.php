<!-- FILE: /app/views/auth/login.php -->
<div class="auth-card">
    <h2>Login</h2>
    <form action="/login" method="POST" class="auth-form">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

    <div class="auth-links">
        <p>Don't have an account? <a href="/register">Register here</a></p>
    </div>
</div>
