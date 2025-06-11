<?php
require_once "connection.php";

// Fetch catalogue items
$sql = "SELECT * FROM farm_catalogue";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmers Catalogue</title>
    <style>
        .catalogue-item {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            display: inline-block;
            width: 300px;
        }
        .catalogue-item img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>Farmers Catalogue</h1>
    
    <?php if (isset($_GET['success'])): ?>
        <p style="color:green;">Item added successfully!</p>
    <?php endif; ?>

    <div class="catalogue">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="catalogue-item">
                    <h3><?= htmlspecialchars($row['itemName']) ?></h3>
                    <p><strong>Farmer:</strong> <?= htmlspecialchars($row['farmerName']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                    <p><strong>Type:</strong> <?= htmlspecialchars($row['itemType']) ?></p>
                    <p><strong>Quantity:</strong> <?= htmlspecialchars($row['quantity']) ?></p>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <?php if ($row['image']): ?>
                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['itemName']) ?>">
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No items found in catalogue</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>