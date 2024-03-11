<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watermarked Image History</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: 20px auto;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            width:60%;
            margin-left:15%;
            
        }

        .image-item {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Watermarked Image History</h1>
        <div class="image-container">
            <?php
            session_start();
            $servername = 'localhost';
            $username = 'root';
            $password = '';
            $dbname = 'watermark_db';

            $user=$_SESSION['user'];

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT image_name FROM watermarked_images WHERE user='$user'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = 'uploads/' . $row['image_name'];
                    echo '<div class="image-item">';
                    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Watermarked Image" style="max-width: 100%;">';
                    echo '</div>';
                }
            } else {
                echo '<p>No watermarked images found.</p>';
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>

</html>
