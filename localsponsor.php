<?php
session_start();
include('conekt.php');

// Initialize filters
$gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';
$street_filter = isset($_GET['street']) ? $_GET['street'] : 'Any';
$hobby_filter = isset($_GET['hobby']) ? $_GET['hobby'] : '';

// Build the SQL query with filters
$sql = "SELECT * FROM children WHERE 1=1"; // Start with a basic query

if ($gender_filter) {
    $sql .= " AND gender = ?";
}
if ($street_filter && $street_filter !== 'Any') {
    $sql .= " AND street = ?";
}
if ($hobby_filter) {
    $sql .= " AND hobby = ?";
}

$stmt = $conn_users->prepare($sql);
$bind_params = [];
$param_types = 's'; // Initialize with the first parameter type

if ($gender_filter) {
    $bind_params[] = $gender_filter;
}
if ($street_filter && $street_filter !== 'Any') {
    $param_types .= 's';
    $bind_params[] = $street_filter;
}
if ($hobby_filter) {
    $param_types .= 's';
    $bind_params[] = $hobby_filter;
}

if ($bind_params) {
    $stmt->bind_param($param_types, ...$bind_params);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a Child</title>
    <link rel="stylesheet" href="z.css">
</head>
<body>
    <h1>Choose a Child for Sponsorship</h1>
    <form action="" method="get">
        <div class="row">
            <div class="gender1">
                <p class="sponsor">I want to sponsor a:</p>
                <div class="gender3">
                    <label><input type="radio" name="gender" value="Boy" <?= $gender_filter === 'Boy' ? 'checked' : '' ?>> Boy</label>
                    <label><input type="radio" name="gender" value="Girl" <?= $gender_filter === 'Girl' ? 'checked' : '' ?>> Girl</label>
                    <br>
                    <label><input type="radio" name="gender" value="" <?= !$gender_filter ? 'checked' : '' ?>> Either</label>
                </div>
            </div>

            <div class="places">
                <p class="sponsor1">Who comes from:</p>
                <select name="street">
                    <option value="Any">All Streets</option>
                    <option value="lemara" <?= $street_filter === 'lemara' ? 'selected' : '' ?>>Lemara</option>
                    <option value="daraja_mbili" <?= $street_filter === 'daraja_mbili' ? 'selected' : '' ?>>Daraja Mbili</option>
                    <option value="sokoni_one" <?= $street_filter === 'sokoni_one' ? 'selected' : '' ?>>Sokoni One</option>
                    <option value="mjini_kati" <?= $street_filter === 'mjini_kati' ? 'selected' : '' ?>>Mjini Kati</option>
                    <option value="njiro" <?= $street_filter === 'njiro' ? 'selected' : '' ?>>Njiro</option>
                    <option value="kijenge" <?= $street_filter === 'kijenge' ? 'selected' : '' ?>>Kijenge</option>
                    <option value="moshono" <?= $street_filter === 'moshono' ? 'selected' : '' ?>>Moshono</option>
                    <option value="uzunguni" <?= $street_filter === 'uzunguni' ? 'selected' : '' ?>>Uzunguni</option>
                </select>
            </div>

            <div class="places">
                <p class="sponsor1">With Hobby:</p>
                <select name="hobby">
                    <option value="">All Hobbies</option>
                    <option value="Music" <?= $hobby_filter === 'Music' ? 'selected' : '' ?>>Music</option>
                    <option value="Dancing" <?= $hobby_filter === 'Dancing' ? 'selected' : '' ?>>Dancing</option>
                    <option value="Cooking & Baking" <?= $hobby_filter === 'Cooking & Baking' ? 'selected' : '' ?>>Cooking & Baking</option>
                    <option value="Swimming" <?= $hobby_filter === 'Swimming' ? 'selected' : '' ?>>Swimming</option>
                    <option value="Drawing" <?= $hobby_filter === 'Drawing' ? 'selected' : '' ?>>Drawing</option>
                    <option value="Sports" <?= $hobby_filter === 'Sports' ? 'selected' : '' ?>>Sports</option>
                    <option value="Reading" <?= $hobby_filter === 'Reading' ? 'selected' : '' ?>>Reading</option>
                    <option value="Gardening" <?= $hobby_filter === 'Gardening' ? 'selected' : '' ?>>Gardening</option>
                    <option value="Computer" <?= $hobby_filter === 'Computer' ? 'selected' : '' ?>>Computer</option>
                    <option value="Photography" <?= $hobby_filter === 'Photography' ? 'selected' : '' ?>>Photography</option>
                    <option value="Writing" <?= $hobby_filter === 'Writing' ? 'selected' : '' ?>>Writing</option>
                </select>
            </div>
        </div>
        <button type="submit">Filter</button>
    </form>

    <h2>Available Children</h2>
    <div>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && $result !== null): ?>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div>
                        <h3><?= htmlspecialchars($row['name']) ?></h3>
                        <p>Age: <?= htmlspecialchars($row['age']) ?></p>
                        <p>Description: <?= htmlspecialchars($row['description']) ?></p>
                        
                        <!-- Fetch images for this child -->
                        <?php
                        $image_sql = "SELECT image_url FROM child_images WHERE child_id = ?";
                        $stmt = $conn_users->prepare($image_sql);
                        $child_id = $row['id'];
                        $stmt->bind_param('i', $child_id);
                        $stmt->execute();
                        $image_result = $stmt->get_result();
                        ?>

                        <?php if ($image_result->num_rows > 0): ?>
                            <div class="child-images">
                                <?php while ($image_row = $image_result->fetch_assoc()): ?>
                                    <div class="child-image">
                                        <img src="uploads/<?= htmlspecialchars($image_row['image_url']) ?>" alt="Child Image" width="100" height="100">
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p>No images available for this child.</p>
                        <?php endif; ?>

                        <a href="sponsor_child.php?child_id=<?= $row['id'] ?>">Sponsor</a>
                    </div>
                    <hr>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No children found based on your filters.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
