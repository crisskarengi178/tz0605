<?php
// Database connection
include 'conekt.php';

// Check if sponsor_id is set
if (!isset($_GET['sponsor_id'])) {
    die("Sponsor ID not provided.");
}

$sponsor_id = intval($_GET['sponsor_id']);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $method = $_POST['method'];
    $account_number = $_POST['account_number'];
    $account_holder_name = $_POST['account_holder_name'];
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO payment_methods (sponsor_id, method, account_number, account_holder_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $sponsor_id, $method, $account_number, $account_holder_name);
    
    if ($stmt->execute()) {
        echo "<p>Payment method has been successfully added.</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <title>Payment Method</title>
</head>
<body>
    <h1>Choose Payment Method</h1>
    <form method="POST" action="">
        <label for="method">Payment Method:</label>
        <select name="method" id="method" required>
            <option value="bank transfer">Bank Transfer</option>
            <option value="mpesa">M-Pesa</option>
            <option value="tigopesa">Tigo Pesa</option>
            <option value="airtel_money">Airtel Money</option>
            <option value="halopesa">Halo Pesa</option>
            <option value="lipanamba">Lipa Na M-Pesa</option>
        </select>
        
        <div id="bank-details" style="display: none;">
            <label for="account_number">Account Number:</label>
            <input type="text" name="account_number" id="account_number" placeholder="Enter account number">
            
            <label for="account_holder_name">Account Holder Name:</label>
            <input type="text" name="account_holder_name" id="account_holder_name" placeholder="Enter account holder name">
        </div>

        <button type="submit">Submit Payment Method</button>
    </form>

    <script>
        const methodSelect = document.getElementById('method');
        const bankDetails = document.getElementById('bank-details');

        methodSelect.addEventListener('change', function() {
            if (this.value === 'bank transfer') {
                bankDetails.style.display = 'block';
            } else {
                bankDetails.style.display = 'none';
            }
        });
    </script>
</body>
</html>
s