<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'public') { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];
$pub_user = $conn->query("SELECT * FROM public_users WHERE user_id = $user_id")->fetch_assoc();

if (isset($_POST['add_funds'])) {
    $amount = (float)$_POST['amount'];
    if ($amount > 0) {
        $conn->query("UPDATE public_users SET wallet_balance = wallet_balance + $amount WHERE user_id = $user_id");
        echo "<b>Funds added successfully!</b>";
        // Refresh balance
        $pub_user['wallet_balance'] += $amount;
    }
}
?>
<a href="my_account.php">Back to My Account</a>
<h2>My Wallet</h2>
<h3>Current Balance: ₹<?php echo number_format($pub_user['wallet_balance'], 2); ?></h3>
<hr>
<h4>Add Funds (Simulated)</h4>
<form method="post">
    Amount to add: <input type="number" name="amount" step="0.01" min="1" required>
    <button type="submit" name="add_funds">Add to Wallet</button>
</form>