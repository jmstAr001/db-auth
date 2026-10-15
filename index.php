<?php
$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <h1>Welcome to the <span class="accent">Django Girls</span> Portal</h1>
    <p class="lead">A friendly, secure space for women and girls in Sierra Leone to learn
       to code, share projects and grow together.</p>
    <div class="hero-actions">
        <?php if (is_logged_in()): ?>
            <a class="btn" href="dashboard.php">Go to my dashboard</a>
        <?php else: ?>
            <a class="btn" href="register.php">Create an account</a>
            <a class="btn btn-outline" href="login.php">Log in</a>
        <?php endif; ?>
    </div>
</section>

<section class="cards">
    <article class="card">
        <h1>Secure by design</h1>
        <p>Passwords are hashed, forms are protected against forgery and sessions end safely.</p>
    </article>
    <article class="card">
        <h1>Works on any device</h1>
        <p>A responsive layout that looks good on phones, tablets and laptops.</p>
    </article>
    <article class="card">
        <h1>Your own dashboard</h1>
        <p>After you log in, see your personal profile, activity and learning tips.</p>
    </article>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
