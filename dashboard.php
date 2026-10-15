<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

$stmt = db()->prepare('SELECT first_name, last_name, email, created_at, last_login, login_count FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {                                     // account removed while logged in
    $_SESSION = [];
    session_destroy();
    session_start();
    flash('error', 'Account not found. Please sign up again.');
    redirect('register.php');
}

$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$initials = strtoupper(mb_substr($user['first_name'], 0, 1) . mb_substr($user['last_name'], 0, 1));
$fmt = fn(?string $d) => $d ? date('D, j M Y \a\t H:i', strtotime($d)) : 'This is your first visit';

$tips = [
    'Practise a little every day - 20 minutes beats 3 hours once a week.',
    'Read error messages slowly. They usually tell you exactly what is wrong.',
    'Build small projects: a to-do list, a quiz, a personal page.',
    'Ask questions and help others - teaching is the fastest way to learn.',
];
$tip = $tips[(int) date('z') % count($tips)];     // a different tip each day

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="dash-hero">
    <div class="avatar" aria-hidden="true"><?= e($initials) ?></div>
    <div>
        <h1><?= e($greeting) ?>, <?= e($user['first_name']) ?>!</h1>
        <p class="muted">Here is a quick overview of your account.</p>
    </div>
</section>

<section class="stats">
    <div class="stat"><span class="stat-label">Total logins</span><span class="stat-value"><?= (int) $user['login_count'] ?></span></div>
    <div class="stat"><span class="stat-label">Member since</span><span class="stat-value small"><?= e(date('j M Y', strtotime($user['created_at']))) ?></span></div>
    <div class="stat"><span class="stat-label">Previous login</span><span class="stat-value small"><?= e($fmt($_SESSION['previous_login'] ?? null)) ?></span></div>
</section>

<section class="dash-grid">
    <article class="panel">
        <h2>Your profile</h2>
        <dl>
            <dt>Full name</dt><dd><?= e($user['first_name'] . ' ' . $user['last_name']) ?></dd>
            <dt>Email</dt><dd><?= e($user['email']) ?></dd>
            <dt>Status</dt><dd><span class="badge">Active</span></dd>
        </dl>
    </article>
    <article class="panel">
        <h2>Tip of the day</h2>
        <p class="tip">&ldquo;<?= e($tip) ?>&rdquo;</p>
        <p class="muted">Sessions end automatically after <?= (int) (SESSION_IDLE_TIMEOUT / 60) ?> minutes of inactivity.</p>
    </article>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
