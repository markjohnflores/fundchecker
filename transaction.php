<?php 
include 'db.php'; 
checkAccess(['SUPER_ADMIN', 'ADMIN']);

$stmt = $pdo->query("SELECT SUM(amount) as bal FROM transactions");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$available_balance = $row['bal'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_SESSION['full_name']; 
    $type = $_POST['transaction_type'];
    $amount = floatval($_POST['amount']);
    $date = date('Y-m-d'); 
    $reason = $_POST['reason'];
    
    $ref = 'SC-' . date('Y') . '-' . rand(100, 999);
    $final_amount = ($type == 'deposit') ? $amount : -$amount;

    $insert = $pdo->prepare("INSERT INTO transactions (reference_no, person_name, amount, transaction_type, reason, transaction_date) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->execute([$ref, $name, $final_amount, strtoupper($type), $reason, $date]);

    header("Location: overview.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SchoolLedger - New Transaction</title>
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
            <?php if ($_SESSION['role'] === 'SUPER_ADMIN'): ?>
                <li><a href="users.php">User Management</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="transaction-layout">
        <section>
            <section class="page-title-section">
                <h2>Deposit / withdrawal</h2>
                <p>Record money in or out. Keep every movement accountable.</p>
                <a href="overview.php" class="back-link">← Back to transactions</a>
            </section>

            <form action="transaction.php" method="POST" class="styled-form">
                <header class="form-header">
                    <h3>Transaction details</h3>
                    <small>* Required fields</small>
                </header>
                
                <section class="form-group">
                    <label>Admin Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" readonly style="background-color: #f5f5f5;">
                    <small class="helper-text">Auto-filled based on your login.</small>
                </section>

                <fieldset class="form-group no-border">
                    <legend>Transaction type *</legend>
                    <section class="radio-cards-container">
                        <article class="radio-card-wrapper">
                            <input type="radio" id="deposit" name="transaction_type" value="deposit" checked>
                            <label for="deposit">
                                <strong>Deposit</strong>
                                <span>Add money to the fund</span>
                            </label>
                        </article>
                        
                        <article class="radio-card-wrapper">
                            <input type="radio" id="withdrawal" name="transaction_type" value="withdrawal">
                            <label for="withdrawal">
                                <strong>Withdrawal</strong>
                                <span>Take money from the fund</span>
                            </label>
                        </article>
                    </section>
                </fieldset>

                <section class="form-row">
                    <article class="form-group">
                        <label for="amount">Amount *</label>
                        <input type="number" id="amount" name="amount" step="0.01" required>
                    </article>

                    <article class="form-group">
                        <label>Date</label>
                        <input type="date" value="<?php echo date('Y-m-d'); ?>" readonly style="background-color: #f5f5f5;">
                        <small class="helper-text">Automatically set to today.</small>
                    </article>
                </section>

                <section class="form-group">
                    <label for="reason">Reason *</label>
                    <textarea id="reason" name="reason" rows="3" required></textarea>
                </section>

                <footer class="form-footer">
                    <span class="user-stamp">Recording as <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <section class="form-buttons">
                        <button type="button" class="btn-cancel" onclick="window.location.href='overview.php'">Cancel</button>
                        <button type="submit" class="btn-submit">Submit transaction</button>
                    </section>
                </footer>
            </form>
        </section>

        <aside id="balance-preview" data-base-balance="<?php echo $available_balance; ?>">
            <h3>Balance preview</h3>
            <p>Current balance: <br><strong>₱<?php echo number_format($available_balance, 2); ?></strong></p>
            <p><span id="preview-label">Deposit amount:</span> <br><strong id="preview-amount" style="color: #1b4d36;">+₱0.00</strong></p>
            <hr style="border: 0; border-top: 1px solid #eaeaea; margin: 20px 0;">
            <p>Balance after transaction: <br><strong id="preview-final">₱<?php echo number_format($available_balance, 2); ?></strong></p>
        </aside>
    </main>

    <script src="script.js"></script>
</body>
</html>