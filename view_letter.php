<?php
session_start();

// Check if the sponsor registration was successful
if (!isset($_SESSION['registration_success']) || !$_SESSION['registration_success']) {
    header("Location: sponsor_child.php"); // Redirect to the registration form if not successful
    exit();
}

// Clear the registration success flag
unset($_SESSION['registration_success']);

// Get the sponsor ID from the session
$sponsor_id = $_SESSION['sponsor_id'];

// Set the path to the generated PDF
$pdf_file = 'sponsorship_agreement_' . $sponsor_id . '.pdf'; // Modify this line to match your naming convention

// Check if the PDF file exists
if (!file_exists($pdf_file)) {
    echo "Error: PDF file not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship Agreement Confirmation</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Include your CSS file here -->
</head>
<body>
    <div class="container">
        <h2>Sponsorship Agreement Confirmation</h2>
        <p>Your sponsorship agreement has been successfully created. You can download it using the link below:</p>
        <a href="<?php echo $pdf_file; ?>" target="_blank" class="btn">Download Sponsorship Agreement</a>
        <p>If you have any questions, please contact us.</p>
        <a href="sponsor.php" class="btn">Register Another Sponsor</a>
    </div>
</body>
</html>
