<?php
require_once 'includes/config.php';

$user_id = 2; // Hardcoded for demonstration; in a real app, this would come from session or GET

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $profile_pic = $_FILES['profile_pic'] ?? null;

    if (!empty($username)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$username, $user_id]);
    }

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
    <title>Profile</title>
</head>

<body>
    <h1>Your Profile, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <p>Profile details here.</p>

    <!-- Icon -->


    <nav>
        <!-- <a href="dashboard.php">Back to Dashboard</a>  --> Back to Dashboard
        <a href="<? /*php logout(); */ ?>">Logout</a>
    </nav>

    <?php if ($user['profile_pic']): ?>
        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" width="150" height="150">
    <?php else: ?>
        <img src="path_to_profile_picture.jpg" alt="Profile Picture" width="150" height="150">
    <?php endif; ?>
    <h2> Username: <?php echo htmlspecialchars($user['username']); ?> </h2>
    <h3> Bio: </h3>

    <h3> Update Profile </h3>
     <form method="POST" enctype="multipart/form-data">
        <label for="username">New Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"><br>
        <label for="profile_pic">New Profile Picture:</label>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/*"><br>
        <button type="submit">Update Profile</button>
    </form>

    <hr>

    <h4> Following List: </h4>
    <ul>
        <li><a href="£"> User 1 </a></li>
        <li><a href="£"> User 2 </a></li>
        <li><a href="£"> User 2 </a></li>
    </ul>

    <h4> Followers List: </h4>
    <ul>
        <li><a href="£"> User A </a></li>
        <li><a href="£"> User B </a></li>
        <li><a href="£"> User C </a></li>
    </ul>

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