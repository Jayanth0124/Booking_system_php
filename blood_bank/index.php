<?php include 'db_connect.php'; ?>
<h1>Blood Management System</h1>
<?php if (isset($_SESSION['user_id'])) {
    $dashboard_link = $_SESSION['role'] . "_dashboard.php";
    echo "<p>Welcome! | <a href='$dashboard_link'>My Dashboard</a> | <a href='logout.php'>Logout</a></p>";
} else {
    echo "<p><a href='login.php'>Login</a> | <a href='register.php'>Register</a></p>";
} ?>
<hr>
<h3>Search for Donors</h3>
<form method="get" action="search_donors.php">
    City: <input type="text" name="city" required>
    Blood Group: 
    <select name="blood_group" required>
        <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
        <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
    </select>
    <button type="submit">Search Donors</button>
</form>
<hr>
<h3>Search for Blood Banks</h3>
<form method="get" action="search_blood_banks.php">
    City: <input type="text" name="city" required>
    <button type="submit">Search Blood Banks</button>
</form>