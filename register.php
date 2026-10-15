<?php
require_once __DIR__ . '/includes/functions.php';
require_guest();

$errors = [];
$old = ['first_name' => '', 'last_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $errors[] = 'Your form expired. Please try again.';
    } else {
        $old['first_name'] = trim($_POST['first_name'] ?? '');
        $old['last_name']  = trim($_POST['last_name'] ?? '');
        $old['email']      = strtolower(trim($_POST['email'] ?? ''));
        $password          = $_POST['password'] ?? '';
        $confirm           = $_POST['confirm_password'] ?? '';

        $namePattern = '/^[\p{L}][\p{L} \'\-]{0,58}$/u';
        if (!preg_match($namePattern, $old['first_name'])) {
            $errors[] = 'Please enter a valid first name (letters only).';
        }
        if (!preg_match($namePattern, $old['last_name'])) {
            $errors[] = 'Please enter a valid last name (letters only).';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || strlen($old['email']) > 190) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            $errors[] = 'Password must be at least 8 characters and include a letter and a number.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!$errors) {
            $check = db()->prepare('SELECT id FROM users WHERE email = ?');
            $check->execute([$old['email']]);
            if ($check->fetch()) {
                $errors[] = 'An account with that email already exists.';
            } else {
                $insert = db()->prepare(
                    'INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)'
                );
                $insert->execute([
                    $old['first_name'],
                    $old['last_name'],
                    $old['email'],
                    password_hash($password, PASSWORD_DEFAULT),   // bcrypt/argon2 + automatic salt
                ]);
                flash('success', 'Account created successfully! You can now log in.');
                redirect('login.php');
            }
        }
    }
}

$pageTitle = 'Sign up';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-card">
    <h1>Create your account</h1>
    <p class="muted">Join the community in under a minute.</p>

    <?php if ($errors): ?>
        <div class="alert alert-error" role="alert">
            <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
        <?= csrf_field() ?>
        <div class="row">
            <div class="field">
                <label for="first_name">First name</label>
                <input type="text" id="first_name" name="first_name" value="<?= e($old['first_name']) ?>" required autocomplete="given-name">
            </div>
            <div class="field">
                <label for="last_name">Last name</label>
                <input type="text" id="last_name" name="last_name" value="<?= e($old['last_name']) ?>" required autocomplete="family-name">
            </div>
        </div>
        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required autocomplete="email">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <small>At least 8 characters, with a letter and a number.</small>
        </div>
        <div class="field">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-block">Sign up</button>
    </form>
    <p class="muted center">Already registered? <a href="login.php">Log in</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
