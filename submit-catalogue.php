<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
require_once "connection.php";

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate required fields
    $required = ['farmerName', 'contact', 'location', 'itemType', 'itemName', 'quantity'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            header("Location: index.html?error=missing_fields");
            exit();
        }
    }
    
    // Sanitize and collect inputs
    $farmerName  = trim($_POST['farmerName']);
    $contact     = trim($_POST['contact']);
    $location    = trim($_POST['location']);
    $itemType    = trim($_POST['itemType']);
    $itemName    = trim($_POST['itemName']);
    $quantity    = trim($_POST['quantity']);
    $description = trim($_POST['description'] ?? '');

    // Handle image upload (optional)
    $imagePath = 'uploads/placeholder.png'; // Default placeholder image
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $imageExt = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $imageExt;
        $imagePath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            // Successfully uploaded
        } else {
            // Use placeholder if upload fails
            error_log("Image upload failed, using placeholder");
        }
    } else {
        // No image uploaded, use placeholder
        if (isset($_FILES['image'])) {
            $errorCode = $_FILES['image']['error'];
            error_log("Image upload error: $errorCode");
        }
    }

    // Prepare SQL
    $sql = "INSERT INTO farm_catalogue 
            (farmerName, contact, location, itemType, itemName, quantity, description, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssss", $farmerName, $contact, $location, $itemType, $itemName, $quantity, $description, $imagePath);

    // Execute and redirect
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: view-catalogue.php?success=1");
        exit();
    } else {
        $stmt->close();
        header("Location: view-catalogue.php?error=insert_failed&dberror=" . urlencode($conn->error));
        exit();
    }
}

$conn->close();
?>