<?php
// Database connection
$servername = "localhost";  // replace with your database servername
$username = "root";  // replace with your database username
$password = "Cloudhosting123@";  // replace with your database password
$dbname = "ass3";  // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Signup functionality
if (isset($_POST['signup'])) {
    $studentName= $_POST['studentName'];
    $studentMatric = $_POST['studentMatric'];
    $studentAge = $_POST['studentAge'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Insert the new user into the students table
    $query = "INSERT INTO students (studentName, studentMatric, studentAge, username, password) VALUES ('$studentName', '$studentMatric', '$studentAge','$username', '$password')";
    if ($conn->query($query) === TRUE) {
        // Signup successful, redirect to the login page
        header("Location: index.php");
        exit();
    } else {
        $error = "Error creating user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <br>
    <br>
    <br>
    <div class="container">
        <h2>Sign Up</h2>
        <?php if (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="studentName">Name:</label>
                <input type="text" name="studentName" required>
            </div>
            <div class="form-group">
                <label for="studentMatric">Matric:</label>
                <input type="text" name="studentMatric" required>
            </div>
            <div class="form-group">
                <label for="studentAge">Age:</label>
                <input type="text" name="studentAge" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" name="signup" value="Sign Up">
            </div>
        </form>
        <div class="login-link">
            <a href="index.php">Already have an account? Log in</a>
        </div>
    </div>
</body>
</html>