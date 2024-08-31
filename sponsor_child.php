

<?php
session_start();
include('conekt.php'); // Include your database connection
require('fpdf.php'); // Include FPDF library

// Initialize variables
$sponsor_id = null;
$profile_picture = '';
$full_name = '';
$gender = '';
$marital_status = '';
$email = '';
$contact = '';
$region_of_residency = '';
$district_of_residency = '';
$ward = '';
$village = '';
$street = '';
$postcode = '';
$house_no = '';
$nida = '';
$guarantor_name = '';
$guarantor_location = '';
$guarantor_contact = '';
$payment_method = '';
$account_number = '';
$account_holder_name = '';
$amount = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Basic Information
    $full_name = mysqli_real_escape_string($conn_users, $_POST['full_name']);
    $gender = mysqli_real_escape_string($conn_users, $_POST['gender']);
    $marital_status = mysqli_real_escape_string($conn_users, $_POST['marital_status']);
    $email = mysqli_real_escape_string($conn_users, $_POST['email']);
    $contact = mysqli_real_escape_string($conn_users, $_POST['contact']);
    
    // Demographic Information
    $region_of_residency = mysqli_real_escape_string($conn_users, $_POST['region_of_residency']);
    $district_of_residency = mysqli_real_escape_string($conn_users, $_POST['district_of_residency']);
    $ward = mysqli_real_escape_string($conn_users, $_POST['ward']);
    $village = mysqli_real_escape_string($conn_users, $_POST['village']);
    $street = mysqli_real_escape_string($conn_users, $_POST['street']);
    $postcode = mysqli_real_escape_string($conn_users, $_POST['postcode']);
    $house_no = mysqli_real_escape_string($conn_users, $_POST['house_no']);
    $nida = mysqli_real_escape_string($conn_users, $_POST['nida']);
    
    // Next of Kin
    $guarantor_name = mysqli_real_escape_string($conn_users, $_POST['guarantor_name']);
    $guarantor_location = mysqli_real_escape_string($conn_users, $_POST['guarantor_location']);
    $guarantor_contact = mysqli_real_escape_string($conn_users, $_POST['guarantor_contact']);
    
    // Payment Information
    $payment_method = mysqli_real_escape_string($conn_users, $_POST['method']);
    $account_number = mysqli_real_escape_string($conn_users, $_POST['account_number']);
    $account_holder_name = mysqli_real_escape_string($conn_users, $_POST['account_holder_name']);
    $amount = mysqli_real_escape_string($conn_users, $_POST['amount']);
    
    // Profile Picture Upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
        $profile_picture = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($profile_picture, PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed_types = array('jpg', 'jpeg', 'gif', 'png');

        // Check if file type is allowed and size is appropriate
        if (in_array($file_type, $allowed_types) && $_FILES['profile_picture']['size'] <= 2000000) {
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture)) {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Error: Only JPG, GIF, and PNG files are allowed, and the file size must be less than 2MB.";
            exit();
        }
    }

    // Insert into sponsors table
    $stmt = $conn_users->prepare("INSERT INTO sponsor_1 (full_name, gender, marital_status, email, contact, region_of_residency, district_of_residency, ward, village, street, postcode, house_no, nida, guarantor_name, guarantor_location, guarantor_contact, profile_picture) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssssssss", $full_name, $gender, $marital_status, $email, $contact, $region_of_residency, $district_of_residency, $ward, $village, $street, $postcode, $house_no, $nida, $guarantor_name, $guarantor_location, $guarantor_contact, $profile_picture);
    
    if ($stmt->execute()) {
        $sponsor_id = $stmt->insert_id; // Get the sponsor ID
        $_SESSION['sponsor_id'] = $sponsor_id; // Store sponsor_id in session
        $_SESSION['registration_success'] = true; // Set registration success flag
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();

    // Create PDF document
    if ($sponsor_id) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Sponsorship Agreement', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Add the image at the top left of the letter
        if (file_exists($profile_picture)) {
            $pdf->Image($profile_picture, 10, 10, 33); // Adjust x, y, and width as needed
        }

        // Add the placeholders with examples
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(30); // Adjust space below the image if necessary
        $pdf->Cell(0, 10, 'Full Name: ' . $full_name, 0, 1);
        $pdf->Cell(0, 10, 'Email: ' . $email, 0, 1);
        $pdf->Cell(0, 10, 'Guarantor Name: ' . $guarantor_name, 0, 1);
        $pdf->Cell(0, 10, 'Guarantor Contact: ' . $guarantor_contact, 0, 1);
        
        // Page 3 content
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Acknowledgment of Agreement', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Acknowledgment content
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, "I, $full_name, have read and understood the terms of the Child Sponsorship Protection Agreement. I agree to the terms stated above.\nSignature: _____________________________\nDate: ______________________________", 0, 'L');
        
        // Page 4 content
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Confirmation of Resident', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Resident confirmation
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, "Mtaa wa _______________________________\nS.L.P __________________________\nSimu: ____________________________\nBarua Pepe: _________________________________\nTarehe: _____________________________________\n\nKwa:\nKituo cha Maendeleo ya Mtoto na Kijana TZ0605\nAnwani ya kituo S.L.P 14406\nYAH: KUTHIBITISHA UTAMBULISHO WA MKAZI WANGU NA KUBALI UFADHILI WAKE KATIKA KUWEZESHA JAMII.\n\nNdugu/Mheshimiwa,\nMimi ni _________________________, Mwenyekiti wa Mtaa wa _________________ katika __________________________.\nKwa heshima na taadhima, napenda kuthibitisha kwamba ndugu _______________________________ ni mkazi halali wa mtaa huu na anafahamika kwetu kama mtu mwenye sifa njema na tabia nzuri.\n\nNimejulishwa kwamba ndugu [Jina la Mwananchi] anapenda kumfadhili [Jina la Mtoto/Kijana] katika kituo chenu cha maendeleo ya mtoto na kijana (TZ0605). Baada ya kufanya mazungumzo na ndugu [Jina la Mwananchi], na baada ya kujiridhisha juu ya dhamira yake njema, napenda kutoa baraka zangu na kuthibitisha kuwa ana nia njema ya kusaidia maendeleo ya ___________________________________ husika.\n\nNinaamini kuwa msaada huu utawezesha mtoto/kijana [Jina la Mtoto] kupata haki zake za msingi za kijamii na hivyo kusaidia katika maendeleo ya jamii.\n\nAsante sana kwa kufahamu.\n\nNdugu Mwenyekiti wa Kituo.\nTarehe: ______________________________", 0, 'L');
        
        // Save PDF
        $pdf_file = 'sponsorship_agreement_' . time() . '.pdf';
        $pdf->Output('F', $pdf_file);
        
        // Save PDF file name to database if needed
        // Optional: Add code to save the PDF file name to the database if required

        // Notify user of successful registration and PDF creation
        echo "Registration successful! You can download your sponsorship agreement: <a href='$pdf_file'>Download PDF</a>";
    } else {
        echo "Registration failed.";
    }
}

// HTML form for completed letter upload
?>
<form action="" method="post" enctype="multipart/form-data">
    <label for="completed_letter">Upload your completed sponsorship agreement:</label>
    <input type="file" name="completed_letter" id="completed_letter" accept=".pdf" required>
    <input type="submit" name="upload_letter" value="Upload Letter">
</form>

<?php
// Handle completed letter upload
if (isset($_POST['upload_letter'])) {
    if (isset($_FILES['completed_letter']) && $_FILES['completed_letter']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'completed_letters/';
        $file_name = time() . '_' . basename($_FILES['completed_letter']['name']);
        $completed_letter = $upload_dir . $file_name;
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['completed_letter']['tmp_name'], $completed_letter)) {
            echo "Your completed sponsorship agreement has been uploaded successfully!";
        } else {
            echo "Error uploading the completed letter.";
        }
    } else {
        echo "Error: Please select a valid file to upload.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship Registration</title>
    <link rel="stylesheet" href="4.css">
</head>
<body>
    <h1>Sponsorship Registration</h1>
    <div class="registration">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="name">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="status1">
                <div class="gender">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="status">
                    <label for="marital_status">Marital Status:</label>
                    <select id="marital_status" name="marital_status" required>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                    </select>
                </div>
            </div>

            <div class="email">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="contact1">
                <div class="contact">
                    <label for="contact">Contact Number:</label>
                    <input type="tel" id="contact" name="contact" required>
                </div>
                <div class="picture">
                    <label for="profile_picture">Profile Picture:</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                </div>
            </div>

            <div class="location1">
                <div class="region">
                    <label for="region_of_residency">Region of Residency:</label>
                    <input type="text" id="region_of_residency" name="region_of_residency" required>
                </div>

                <div class="district">
                    <label for="district_of_residency">District of Residency:</label>
                    <input type="text" id="district_of_residency" name="district_of_residency" required>
                </div>
            </div>

            <div class="location2">
                <div class="ward">
                    <label for="ward">Ward:</label>
                    <input type="text" id="ward" name="ward" required>
                </div>
                
                <div class="village">
                    <label for="village">Village:</label>
                    <input type="text" id="village" name="village" required>
                </div>

                <div class="street">
                    <label for="street">Street:</label>
                    <input type="text" id="street" name="street" required>
                </div>
            </div>

            <div class="house1">
                <div class="postcode">
                    <label for="postcode">Postcode:</label>
                    <input type="text" id="postcode" name="postcode" required>
                </div>

                <div class="house">
                    <label for="house_no">House Number:</label>
                    <input type="text" id="house_no" name="house_no" required>
                </div>
            </div>

            <div class="nida">
                <label for="nida">NIDA Number:</label>
                <input type="text" id="nida" name="nida" required>
            </div>

            <div class="guarantor1">
                <div class="guarantor">
                    <label for="guarantor_name">Guarantor Name:</label>
                    <input type="text" id="guarantor_name" name="guarantor_name" required>
                </div>
                
                <div class="location">
                    <label for="guarantor_location">Guarantor Location:</label>
                    <input type="text" id="guarantor_location" name="guarantor_location" required>
                </div>

                <div class="gcontact">
                    <label for="guarantor_contact">Guarantor Contact:</label>
                    <input type="tel" id="guarantor_contact" name="guarantor_contact" required>
                </div>
            </div>

            <h2>Payment Information</h2>

            <div class="payment">
                <div class="account">
                    <label for="method">Payment Method:</label>
                    <select id="method" name="method" required>
                        <option value="bank">Bank Transfer</option>
                        <option value="mobile">Mobile Payment</option>
                        <option value="credit">Credit Card</option>
                    </select>
                </div>

                <div class="accname">
                    <label for="account_number">Account Number:</label>
                    <input type="text" id="account_number" name="account_number" required>
                </div>

                <div class="amount">
                    <label for="account_holder_name">Account Holder Name:</label>
                    <input type="text" id="account_holder_name" name="account_holder_name" required>
                </div>

                <div class="amount">
                    <label for="amount">Amount:</label>
                    <input type="number" id="amount" name="amount" required>
                </div>
            </div>

            <input class="button" type="submit" name="register" value="Register">
        </form>
    </div>

    <div class="credentials">
        <h3>Upload Completed Sponsorship Agreement</h3>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="completed_letter">Upload your completed sponsorship agreement:</label>
            <input type="file" name="completed_letter" id="completed_letter" accept=".pdf" required>
            <input class="button2" type="submit" name="upload_letter" value="Upload Letter">
        </form>
    </div>
</body>
</html>
