<?php
session_start();
session_regenerate_id(true);
require_once 'includes/config.php';


$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        // Secure DB lookup and password_verify()
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username']];
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
</head>

<body>
    <main class="card" role="main">
        <h1>Sign in</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul style="margin:0 0 0 18px; padding:0;">
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="#">
            
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="username">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password">

            <button type="submit">Login</button>
        </form>
    </main>

    <p> Don't have an account? <a href="register.php"> Register here </a> </p>

    <h2> Hello and Welcome to LightCast! </h2>
</body>

</html>