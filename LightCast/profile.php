<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

check_login();

$user_id = $_SESSION['user']['id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if (!empty($username)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$username, $user_id]);
    }

    $profile_pic = $_FILES['profile_pic'] ?? null;
    if ($profile_pic && $profile_pic['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($profile_pic['type'], $allowed_types) && $profile_pic['size'] <= 2 * 1024 * 1024) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_ext = pathinfo($profile_pic['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('profile_', true) . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($profile_pic['tmp_name'], $file_path)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                $stmt->execute([$file_path, $user_id]);
            }
        }
    }

    header("Location: profile.php");
    exit;
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// Fetch posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>

<body>
    <h1>Your Profile, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <!-- Icon -->


    <nav>
        <a href="dashboard.php">Back to Dashboard</a>
        <a href="login.php?logout=1">Logout</a>
    </nav>

    <?php if ($user['profile_pic']): ?>
        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" width="150" height="150">
    <?php else: ?>
        <img src="#" alt="Profile Picture" width="150" height="150"> <!-- Need to add a empty profile pic icon incase there is'nt a profile pic. -->
    <?php endif; ?>
    <h2> Username: <?php echo htmlspecialchars($user['username']); ?> </h2>
    <strong><a href="#" onclick="document.getElementById('popup').style.display='flex'"> Update profile info </a></strong>


    <!-- Overlapping Section -->
    <div class="overlay" id="popup">
        <div class="modal">
            <h3><u> Update Profile </u></h3>
            <form method="post" action="profile.php" enctype="multipart/form-data">


                <label for="username">
                    Profile username:
                </label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                <br>
                <br>

                <label for="profile_pic">
                    Profile picture:
                </label>
                <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                <br>
                <br>

                <button type="submit" class="btnConfirm" onclick="document.getElementById('popup').style.display='none'">
                    Proceed
                </button>

            </form>
        </div>
    </div>

    <hr>

    <table>
        <tr>
            <th> Following List: </th>
            <th> Followers List: </th>
        </tr>

        <tr>
            <td>
                <li><a href="#"> User 1 </a></li>
                <li><a href="#"> User 2 </a></li>
                <li><a href="#"> User 3 </a></li>
            </td>

            <td>
                <li><a href="#"> User A </a></li>
                <li><a href="#"> User B </a></li>
                <li><a href="#"> User C </a></li>
            </td>
        </tr>
    </table>

    <hr>

    <h4> Posts: </h4>

    <table border="1">
        <?php if (count($posts) > 0): ?>
            <?php foreach (array_chunk($posts, 2) as $row): ?>
                <tr>
                    <?php foreach ($row as $post): ?>
                        <td>
                            <?php if ($user['profile_pic']): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" width="50" height="50"> <br>
                            <?php endif; ?>
                            content: <p><?php echo htmlspecialchars($post['content']); ?></p>
                            Timestamp: <?php echo htmlspecialchars($post['created_at']); ?>
                        </td>
                    <?php endforeach; ?>
                    <?php if (count($row) == 1): ?>
                        <td></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">No posts yet.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>

</html>