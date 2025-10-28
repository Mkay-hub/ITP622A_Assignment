<?php
require_once 'includes/config.php';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $profile_pic = $_FILES['profile_pic'] ?? null;

    // Backend form validation
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3 || strlen($username) > 25) {
        $errors[] = 'Username must be between 3 and 50 characters.';
    }

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$profile_pic || $profile_pic['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Profile picture is required.';
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($profile_pic['type'], $allowed_types)) {
            $errors[] = 'Profile picture must be a JPEG, PNG, or GIF image.';
        }
        if ($profile_pic['size'] > 2 * 1024 * 1024) { // 2MB limit
            $errors[] = 'Profile picture must be less than 2MB.';
        }
    }

    if (empty($errors)) {
        // Check uniqueness
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists.';
        } else {
            // Handle file upload
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_ext = pathinfo($profile_pic['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('profile_', true) . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($profile_pic['tmp_name'], $file_path)) {
                // Hash password and insert
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, profile_pic) VALUES (?, ?, ?, ?)');
                if ($stmt->execute([$username, $email, $password_hash, $file_path])) {
                    $success = 'Registration successful! You can now log in.';
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            } else {
                $errors[] = 'Failed to upload profile picture.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            form.addEventListener('submit', function(event) {
                const username = document.getElementById('username').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const profilePic = document.getElementById('profile_pic').files[0];

                let errors = [];

                if (!username) {
                    errors.push('Username is required.');
                } else if (username.length < 3 || username.length > 25) {
                    errors.push('Username must be between 3 and 50 characters.');
                }

                if (!email) {
                    errors.push('Email is required.');
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    errors.push('Invalid email format.');
                }

                if (!password) {
                    errors.push('Password is required.');
                } else if (password.length < 6) {
                    errors.push('Password must be at least 6 characters.');
                }

                if (password !== confirmPassword) {
                    errors.push('Passwords do not match.');
                }

                if (!profilePic) {
                    errors.push('Profile picture is required.');
                } else {
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(profilePic.type)) {
                        errors.push('Profile picture must be a JPEG, PNG, or GIF image.');
                    }
                    if (profilePic.size > 2 * 1024 * 1024) {
                        errors.push('Profile picture must be less than 2MB.');
                    }
                }

                if (errors.length > 0) {
                    event.preventDefault();
                    alert(errors.join('\n'));
                }
            });
        });
    </script>
</head>

<body>
    <header>
        <h1> Registration </h1>
    </header>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color: green;"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="#" method="POST" enctype="multipart/form-data" id="registrationForm">
        <label for="username"> Username: </label>
        <input type="text" id="username" name="username" required> <br>

        <label for="email"> Email: </label>
        <input type="email" id="email" name="email" required> <br>

        <label for="password"> Password: </label>
        <input type="password" id="password" name="password" required> <br>

        <label for="confirm_password"> Confirm Password: </label>
        <input type="password" id="confirm_password" name="confirm_password" required> <br>

        <label for="profile_pic"> Profile picture: </label>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/*" required> <br>

        <button type="submit">Register</button>
    </form>


    <p> Already a member?
        <!-- <a href="login.php">Sign_in</a>  --> Sign in here!
    </p>

    <h1> Hello, Welcome to LightCast </h1>




    <footer>
        T's & C's LightCast corporate
    </footer>

</body>

</html>