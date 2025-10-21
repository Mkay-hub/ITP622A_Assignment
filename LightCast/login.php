<?php
session_start();

// Simple login template for login.php
// NOTE: Replace the demo auth below with real DB lookup and password_verify()

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF check
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request (CSRF token mismatch).';
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        // Demo authentication: replace with secure DB check and password_verify()
        if ($username === 'admin' && $password === 'password') {
            // Prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user'] = ['username' => $username];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background:#f5f7fa; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; }
        .card { background:#fff; padding:20px; border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.08); width:320px; }
        .errors { background:#ffecec; color:#d8000c; padding:8px; border-radius:4px; margin-bottom:12px; }
        label { display:block; margin-top:8px; font-size:14px; }
        input[type="text"], input[type="password"] { width:100%; padding:8px; margin-top:6px; box-sizing:border-box; }
        button { margin-top:12px; width:100%; padding:10px; background:#0078d4; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    </style>
</head>
<body>
    <main class="card" role="main">
        <h2>Sign in</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul style="margin:0 0 0 18px; padding:0;">
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="username">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password">

            <button type="submit">Login</button>
        </form>
    </main>
</body>
</html>