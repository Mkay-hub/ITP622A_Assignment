<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

check_login();

$current_user_id = $_SESSION['user']['id'];

$errors = [];
$search_results = [];
$conversation = [];
$receiver_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search'])) {
        $search_term = trim($_POST['search_term'] ?? '');
        if (!empty($search_term)) {
            $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE username LIKE ? OR email LIKE ? AND id != ?");
            $stmt->execute(['%' . $search_term . '%', '%' . $search_term . '%', $current_user_id]);
            $search_results = $stmt->fetchAll();
        }
    } elseif (isset($_POST['send_message'])) {
        $receiver_id = (int)$_POST['receiver_id'];
        $message = trim($_POST['message']);
        if (!empty($message) && $receiver_id > 0) {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, text_message) VALUES (?, ?, ?)");
            $stmt->execute([$current_user_id, $receiver_id, $message]);
            // Redirect to refresh conversation
            header("Location: messages.php?chat=$receiver_id");
            exit;
        } else {
            $errors[] = 'Message cannot be empty.';
        }
    }
}

if (isset($_GET['chat'])) {
    $receiver_id = (int)$_GET['chat'];
    // Verify the receiver exists and is not the current user
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ? AND id != ?");
    $stmt->execute([$receiver_id, $current_user_id]);
    $receiver = $stmt->fetch();
    if ($receiver) {
        // Fetch conversation
        $stmt = $pdo->prepare("SELECT m.*, u.username AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.sent_at ASC");
        $stmt->execute([$current_user_id, $receiver_id, $receiver_id, $current_user_id]);
        $conversation = $stmt->fetchAll();
    } else {
        $errors[] = 'Invalid chat recipient.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private Messages</title>
</head>

<body>
    <h1>Private Messaging</h1>
    <a href="dashboard.php">Back to Dashboard</a> | <a href="login.php?logout=1">Logout</a>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h2>Search for a User</h2>
    <form method="POST">
        <input type="text" name="search_term" placeholder="Enter username or email" required>
        <button type="submit" name="search">Search</button>
    </form>

    <?php if (!empty($search_results)): ?>
        <h3>Search Results</h3>
        <ul>
            <?php foreach ($search_results as $user): ?>
                <li>
                    <?php echo htmlspecialchars($user['username']) . ' (' . htmlspecialchars($user['email']) . ')'; ?>
                    <a href="messages.php?chat=<?php echo $user['id']; ?>">Start Chat</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($receiver_id && isset($receiver)): ?>
        <h2>Chat with <?php echo htmlspecialchars($receiver['username']); ?></h2>
        <div style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;">
            <?php foreach ($conversation as $msg): ?>
                <div>
                    <strong><?php echo htmlspecialchars($msg['sender_name']); ?>:</strong>
                    <?php echo htmlspecialchars($msg['text_message']); ?>
                    <small>(<?php echo htmlspecialchars($msg['sent_at']); ?>)</small>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST">
            <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
            <textarea name="message" rows="3" cols="50" placeholder="Type your message..." required></textarea><br>
            <button type="submit" name="send_message">Send</button>
        </form>
    <?php endif; ?>
</body>

</html>