<?php
require_once __DIR__ . '/includes/functions.php';
require_guest();

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $locked = ($_SESSION['lock_until'] ?? 0) > time();

    if (!csrf_valid()) {
        $errors[] = 'Your form expired. Please try again.';
    } elseif ($locked) {
        $errors[] = 'Too many failed attempts. Please wait a minute and try again.';
    } else {
        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);              // prevents session fixation
            unset($_SESSION['login_attempts'], $_SESSION['lock_until']);

            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            }
            db()->prepare('UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?')
                ->execute([$user['id']]);

            $_SESSION['user_id']       = (int) $user['id'];
            $_SESSION['last_activity'] = time();
            $_SESSION['previous_login'] = $user['last_login'];
            flash('success', 'Welcome back, ' . $user['first_name'] . '!');
            redirect('dashboard.php');
        }

        // Generic message: never reveal which of email/password was wrong
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION['lock_until'] = time() + LOCKOUT_SECONDS;
            $_SESSION['login_attempts'] = 0;
        }
        $errors[] = 'Incorrect email or password.';
    }
}

$pageTitle = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <h1>Welcome back</h1>
    <p class="muted">Log in to see your dashboard.</p>

    <?php if ($errors): ?>
        <div class="alert alert-error" role="alert">
            <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
        <?= csrf_field() ?>
        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email" autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-block">Log in</button>
    </form>
    <p class="muted center">New here? <a href="register.php">Create an account</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
