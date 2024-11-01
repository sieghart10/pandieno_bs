<html lang="HTML5">
<head>
    <title>PHP Quick Start</title>
</head>
<body>
<?php

require __DIR__ . '/vendor/autoload.php';

// Use the necessary classes
use Cloudinary\Configuration\Configuration;
use Dotenv\Dotenv;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Background;
use Cloudinary\Tag\ImageTag;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get the Cloudinary URL from the environment variables
$cloudinaryUrl = $_ENV['CLOUDINARY_URL'];

// Configure an instance of your Cloudinary cloud
Configuration::instance($cloudinaryUrl);

// Use the AdminApi class for managing assets
$admin = new AdminApi();

// Get the asset ID from the query parameter, default to a placeholder
$assetId = isset($_GET['id']) ? $_GET['id'] : '';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['asset_id'])) {
    $assetId = $_POST['asset_id'];
}

try {
    // Get the asset details
    if ($assetId) {
        $assetDetails = $admin->asset($assetId, [
            'colors' => true
        ]);

        // Display the asset details
        echo '<pre>';
        echo json_encode($assetDetails, JSON_PRETTY_PRINT);
        echo '</pre>';
        
        // Create the image tag with the transformed image
        $imgtag = (new ImageTag($assetId))
            ->resize(Resize::pad()
                ->width(400)
                ->height(400)
                ->background(Background::predominant())
            );

        // Output the image tag
        echo $imgtag;
    } else {
        echo 'Please enter a valid asset ID.';
    }
} catch (\Exception $e) {
    // Handle any exceptions
    echo 'Error: ' . $e->getMessage();
}

?>

<!-- Form for user input -->
<form method="POST" action="">
    <label for="asset_id">Enter Asset ID:</label>
    <input type="text" id="asset_id" name="asset_id" required>
    <button type="submit">Submit</button>
</form>

</body>
</html>
