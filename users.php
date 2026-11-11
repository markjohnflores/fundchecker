<?php
include 'db.php';
checkAccess(['SUPER_ADMIN']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    // The PHP now looks for the disguised 'new_system_user' field
    $username = trim($_POST['new_system_user']); 
    $fullname = trim($_POST['full_name']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $password, $fullname, $role]);
    header("Location: users.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) { 
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: users.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SchoolLedger - Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <p>Cedarfield High School - Student Council</p>
        <p>Signed in as: <?php echo htmlspecialchars($_SESSION['full_name']); ?> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="logout.php">Logout</a></p>
    </header>

    <nav>
        <h2>SchoolLedger</h2>
        <ul>
            <li><a href="overview.php">Overview</a></li>
            <li><a href="transaction.php">Transactions</a></li>
            <li><a href="users.php">User Management</a></li>
        </ul>
    </nav>

    <main>
        <section class="page-title-section">
            <h2>User Management</h2>
            <p>Add or remove system users.</p>
        </section>

        <section class="transaction-layout">
            <form action="users.php" method="POST" class="styled-form login-form" autocomplete="off">
                <header class="form-header">
                    <h3>Add New User</h3>
                </header>
                <section class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="login-input" required>
                </section>
                
                <!-- Disguised username field to stop browser interference -->
                <section class="form-group">
                    <label for="new_system_user">Username</label>
                    <input type="text" id="new_system_user" name="new_system_user" class="login-input" autocomplete="new-password" required>
                </section>
                
                <section class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="login-input" autocomplete="new-password" required>
                </section>
                
                <fieldset class="form-group no-border">
                    <legend>Role</legend>
                    <select name="role" class="login-input" required style="padding: 12px; font-family: inherit;">
                        <option value="ADMIN">Admin (Can transact)</option>
                        <option value="USER">User (View only)</option>
                        <option value="SUPER_ADMIN">Super Admin (Full control)</option>
                    </select>
                </fieldset>
                
                <button type="submit" name="add_user" class="btn-submit login-btn" style="margin-top: 24px;">Add User</button>
            </form>

            <aside>
                <h3>Active Users</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $stmt = $pdo->query("SELECT * FROM users");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>{$row['full_name']}<br><small>{$row['username']}</small></td>";
                                echo "<td>{$row['role']}</td>";
                                echo "<td>";
                                if ($row['id'] != $_SESSION['user_id']) {
                                    echo "<a href='users.php?delete={$row['id']}' style='color:#c62828; text-decoration:none; font-weight:bold;'>Delete</a>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </aside>
        </section>
    </main>
    <script src="script.js"></script>
</body>
</html>