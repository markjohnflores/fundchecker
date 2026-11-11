<?php 
include 'db.php'; 
checkAccess(['SUPER_ADMIN', 'ADMIN', 'USER']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SchoolLedger - Fund Overview</title>
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
            <?php if ($_SESSION['role'] !== 'USER'): ?>
                <li><a href="transaction.php">Transactions</a></li>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'SUPER_ADMIN'): ?>
                <li><a href="users.php">User Management</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Fund overview</h2>
            <p>A clear view of our funds. A shared responsibility.</p>
        </section>

        <?php
            $stmt = $pdo->query("SELECT * FROM transactions ORDER BY transaction_date DESC");
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $available_balance = 0;
            $total_deposits = 0; $deposit_count = 0;
            $total_withdrawals = 0; $withdrawal_count = 0;

            foreach ($transactions as $txn) {
                $available_balance += $txn['amount'];
                if ($txn['transaction_type'] == 'DEPOSIT') {
                    $total_deposits += $txn['amount'];
                    $deposit_count++;
                } else {
                    $total_withdrawals += abs($txn['amount']);
                    $withdrawal_count++;
                }
            }
        ?>

        <section>
            <article>
                <h3>Available balance (GENERAL FUND)</h3>
                <p>₱<?php echo number_format($available_balance, 2); ?></p>
                <?php if ($_SESSION['role'] !== 'USER'): ?>
                    <a href="transaction.php?type=deposit">+ Add deposit</a>
                    <a href="transaction.php?type=withdraw">↗ Withdraw funds</a>
                <?php endif; ?>
            </article>

            <article>
                <h3>Total deposits</h3>
                <p>₱<?php echo number_format($total_deposits, 2); ?></p>
                <p><?php echo $deposit_count; ?> deposits total</p>
            </article>

            <article>
                <h3>Total withdrawals</h3>
                <p>₱<?php echo number_format($total_withdrawals, 2); ?></p>
                <p><?php echo $withdrawal_count; ?> withdrawals total</p>
            </article>
        </section>

        <section>
            <h2>Transaction history</h2>
<form id="search-form" action="#" method="GET" style="margin-bottom: 24px;">
                <input type="text" id="table-search" class="login-input" placeholder="Search name, reason, or reference...">
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Name (Admin)</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Date</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $running_balance = $available_balance;
                        foreach ($transactions as $txn) {
                            echo "<tr>";
                            echo "<td>{$txn['person_name']}<br><small>{$txn['reference_no']}</small></td>";
                            $amount_str = $txn['amount'] > 0 ? "+₱" . number_format($txn['amount'], 2) : "-₱" . number_format(abs($txn['amount']), 2);
                            $color_style = $txn['amount'] > 0 ? "style='color: #1b4d36; font-weight: bold;'" : "";
                            echo "<td {$color_style}>{$amount_str}</td>";
                            echo "<td>{$txn['transaction_type']}</td>";
                            echo "<td>{$txn['reason']}</td>";
                            echo "<td>" . date('d M Y', strtotime($txn['transaction_date'])) . "</td>";
                            echo "<td>₱" . number_format($running_balance, 2) . "</td>";
                            echo "</tr>";
                            $running_balance -= $txn['amount'];
                        }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>