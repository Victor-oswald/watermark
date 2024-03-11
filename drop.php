<?php
session_start();
$responseMessage = '';

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'watermark_db';
$user=isset($_SESSION['user'])?$_SESSION['user']:"GUEST";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDirectory = 'uploads/';

        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDirectory . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $watermarkText = isset($_POST['watermarkText']) ? $_POST['watermarkText'] : 'Watermark';

            addTextWatermark($targetFile, $watermarkText);
            $sql = "INSERT INTO watermarked_images (user,image_name) VALUES ('$user','$imageName')";
            if ($conn->query($sql) === TRUE) {
                $responseMessage = '<h2>Watermarked Image</h2>';
                $responseMessage .= '<img src="' . $targetFile . '" alt="Watermarked Image" style="max-width: 100%;">';
                $responseMessage .= '<a href="' . $targetFile . '" download="watermarked_image.jpg">';
                $responseMessage .= '<button>Download Watermarked Image</button>';
                $responseMessage .= '</a>';
            } else {
                $responseMessage = 'Error uploading the image.';
            }
        } else {
            $responseMessage = 'Error uploading the image.';
        }
    } else {
        $responseMessage = 'Please choose an image to upload.';
    }
}

function addTextWatermark($imagePath, $text)
{
    list($width, $height, $type) = getimagesize($imagePath);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($imagePath);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($imagePath);
            break;
        default:
            throw new Exception('Unsupported image type.');
    }

    $fontSize = 10;
    $fontColor = imagecolorallocatealpha($image, 255, 255, 255, 20);

    $positions = [
        ['x' => 10, 'y' => 15],
        ['x' => 50, 'y' => 50],
        ['x' => 60, 'y' => 80],
        ['x' => $width / 2, 'y' => $height / 2, 'center' => true], // Center watermark
        ['x' => $width - 10, 'y' => 10],
        ['x' => 10, 'y' => $height - 10],
        ['x' => $width - 10, 'y' => $height - 10]
    ];

    foreach ($positions as $position) {
        $x = $position['x'];
        $y = $position['y'];

        if (isset($position['center']) && $position['center']) {
            $textWidth = strlen($text) * $fontSize * 0.6;
            $textHeight = $fontSize;

            $x = ($width - $textWidth) / 2;

            $y = ($height - $textHeight) / 2;
        }

        imagettftext($image, $fontSize, 0, $x, $y, $fontColor, 'UbuntuCondensed-Regular.ttf', $text);
    }

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($image, $imagePath);
            break;
        case IMAGETYPE_PNG:
            imagepng($image, $imagePath);
            break;
        case IMAGETYPE_GIF:
            imagegif($image, $imagePath);
            break;
    }

    imagedestroy($image);
}



$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Image Drop Zone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        header {
            background-color: transparent;
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.3);
        }

        header ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        header ul li {
            display: inline;
            padding: 0 15px;
        }

        header button {
            background-color: grey;
            border: none;
            cursor: pointer;
            padding: 10px 24px;
            font-size: 16px;
            color: white;
        }

        .dropdown {
            display: inline-block;
        }

        .dropdown-content button {
            display: block;
            width: 100%;
            padding: 8px 10px;
            border: none;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-content button:hover {
            background-color: #ddd;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        #drop-zone {
            width: 300px;
            height: 300px;
            border: 2px dashed #bbb;
            border-radius: 5px;
            text-align: center;
            padding: 10px;
            margin: 20px auto;
            
        }

        .dropdown-content {
            display: none;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            padding: 10px;
            position: absolute;
            z-index: 10;
            top: 80px;
            background-color: #ffff;
            width: 100%;
            height: 100%;
            right: 0;
        }

        .dropdown-content ul li {
            display: grid;
            text-align: left;
            line-height: 5.5;
            cursor: pointer;
            border-bottom: 1px solid rgba(0, 0, 0, 0.103);
        }

        .dropdown:hover .dropdown-content,
        .dropdown-btn:active+.dropdown-content {
            display: grid;
        }

        .dropdown .dropdown-btn {
            display: none;
        }
      .img-sel{
        width:70%;
        float:right;
        margin-left:10%;
        box-shadow: 0px 3px 10px -8px  rgba(0,0,0,10);
        padding:30px;
      }
      .body{
        display:grid;
        grid-template-columns:1fr 1fr;
      }

        @media screen and (max-width: 600px) {
            .dropdown .dropdown-btn {
                display: block;
                background-color: transparent;
                text-align: right;
                color: black;
                font-size: 30px;
            }

            .dropdown {
                float: right;
                padding: none;
            }

            .spread div {
                display: none;
            }

            header div ul li {
                display: block;
            }

            .dropdown-content {
                display: none;
            }

            .dropdown-btn:active+.dropdown-content {
                display: block;
            }

            .dropdown-btn {
                display: block;
            }

            .form {
                width: 70%;
                display: block;
                margin: auto;
            }
            .body{
        display:block;
        text-align:center;
      }
      .img-sel{
        width:100%;
        float:none;
        margin-left:0;
        box-shadow: 0px 3px 10px -8px  rgba(0,0,0,10);
        padding:10px;
      }

        }
    </style>
    <style>
        h1 {
            color: #333;
            margin-bottom: 30px;
        }

        .upload-section,
        .watermark-section,
        .output-section {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .upload-section label {
            cursor: pointer;
            color: #ffffff;
            padding: 20px;
            background-color: #2980b9;
            border-radius: 10px;
        }

        .upload-section input[type="file"] {
            display: none;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            color: #fff;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        #outputImage {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .response-message {
            margin-top: 10px;
            color: green;
        }

        .error-message {
            margin-top: 10px;
            color: red;
        }
    </style>
</head>

<body>
    <header>
        <ul class="spread">
            <div>
                <li>Documentation</li>
                <li>Pricing</li>
                <li>API</li>
                <li>Log in</li>
                <li>Sign Up</li>
            </div>
            <li class="dropdown">
                <button class="dropdown-btn">☰</button>
                <div class="dropdown-content">
                    <ul>

                        <li>Documentation</li>
                        <li>Pricing</li>
                        <li>API</li>
                        <li>Log in</li>
                        <li>Sign Up</li>

                    </ul>
                </div>
            </li>
        </ul>
    </header>
    <section class="body">
        <section>
            <h1 style="font-size:3em; font-weight:bold">Welcome to Oswald digital solutions, we enable you to protect your digital content for safe publication.</h1>
        </section>

<section class="img-sel">
    <div id="drop-zone">
        <p>Drag and drop an image here</p>
    </div>
    <h2 style="text-align: center;">OR</h2>
    <div class="form">
        <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="upload-section">
                <label for="imageInput" id="imageLabel">Click here to choose image:</label>
                <input type="file" name="image" id="imageInput" accept="image/*" onchange="updateImageLabel()">
            </div>
            <div class="watermark-section">
                <label for="watermarkText">Watermark Text:</label>
                <input type="text" name="watermarkText" id="watermarkText" placeholder="Enter your watermark text">
                <button type="submit" id="applyWatermark">Apply Watermark</button>
            </div>
        </form>
    </div>
    <div class="response-message"><?php echo $responseMessage; ?></div>

    </section>
    </section>
    <script>
        const dropZone = document.getElementById('drop-zone');

        function updateImageLabel() {
            const imageInput = document.getElementById('imageInput');
            const imageLabel = document.getElementById('imageLabel');

            if (imageInput.files.length > 0) {
                const selectedImage = URL.createObjectURL(imageInput.files[0]);
                const img = document.createElement('img');
                img.src = selectedImage;
                dropZone.appendChild(img);
            } else {
                imageLabel.innerHTML = 'Click here to choose image:';
            }
        }

        dropZone.addEventListener('dragover', (event) => {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'copy';
        });

        dropZone.addEventListener('drop', (event) => {
            event.preventDefault();

            const files = event.dataTransfer.files;

            if (files.length > 0) {
                const file = files[0];

                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    dropZone.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });

        document.querySelectorAll('.dropdown-btn').forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation();
                const dropdownContent = this.nextElementSibling;
                dropdownContent.style.display = dropdownContent.style.display === 'grid' ? 'none' : 'grid';
            });
        });

        document.addEventListener('click', function (event) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target) && window.innerWidth > 600) {
                    const dropdownContent = dropdown.querySelector('.dropdown-content');
                    dropdownContent.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>