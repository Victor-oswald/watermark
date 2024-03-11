

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Watermarking Tool</title>

</head>
<body>
    <div class="container">
        <header>
            <img src="logo.png" alt="Logo" style="max-width: 100%; width:40%" onclick="window.location.href='main_page.html'">
        </header>

        <div class="footer">
            <button onclick="window.location.href='history.php'">History</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function () {
    fetch('usercheck.php', {
        method: 'GET'
    })
        .then(response => response.json())
        .then(data => {
            console.log('Response from logout.php (GET):', data);

            if (data.status === 'error' && data.message === 'User session is not set.') {
                window.location.href = 'login.html';
            }
        })
        .catch(error => {
            console.error('Error making GET request to logout.php:', error);
        });
});
    </script>
</body>

</html>


</html>

