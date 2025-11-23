<!-- FILE: /app/views/auth/register.php -->
<div class="auth-card">
    <h2>Create Account</h2>
    <form action="/register" method="POST" class="auth-form">
        <?php echo CSRF::field(); ?>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required class="form-control">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6" class="form-control">
        </div>

        <div class="form-group">
            <label for="company_name">Company Name (Optional)</label>
            <input type="text" id="company_name" name="company_name" class="form-control">
        </div>

        <div class="form-group">
            <label for="plan_id">Select Plan</label>
            <select id="plan_id" name="plan_id" class="form-control">
                <?php foreach ($plans as $plan): ?>
                    <option value="<?php echo $plan['id']; ?>">
                        <?php echo htmlspecialchars($plan['name']); ?> - $<?php echo $plan['price_monthly']; ?>/mo
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>

    <div class="auth-links">
        <p>Already have an account? <a href="/login">Login here</a></p>
    </div>
</div>
