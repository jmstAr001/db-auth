<?php
require_once __DIR__ . '/includes/functions.php';

// Logout only via POST + valid CSRF token so other sites cannot log a user out.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valid()) {
    redirect(is_logged_in() ? 'dashboard.php' : 'login.php');
}

$_SESSION = [];                                   // clear all session data
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();                                // destroy the server-side session

session_start();                                  // fresh session just to show a message
session_regenerate_id(true);
flash('success', 'You have been logged out safely. See you soon!');
redirect('login.php');
