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

// Check if the student is logged in
session_start();
if (!isset($_SESSION['studentId'])) {
    header("Location: login.php");
    exit();
}

// Fetch student and registered subject data
$studentId = $_SESSION['studentId'];

// Fetch student data
$query = "SELECT * FROM students WHERE studentId='$studentId'";
$studentResult = $conn->query($query);
$studentData = $studentResult->fetch_assoc();

// Registration functionality
if (isset($_POST['register'])) {
    $studentId = $_SESSION['studentId'];
    $subjectsId = $_POST['subjectsId'];

    // Check if the student has already registered for the selected subject
    $query = "SELECT * FROM registrations WHERE studentId='$studentId' AND subjectsId='$subjectsId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $error = "You have already registered for this subject";
    } else {
        // Insert the registration into the registrations table
        $insertQuery = "INSERT INTO registrations (studentId, subjectsId) VALUES ('$studentId', '$subjectsId')";
        if ($conn->query($insertQuery) === true) {
            $success = "Subject registered successfully";
        } else {
            $error = "Error registering subject: " . $conn->error;
        }
    }
}

// Fetch all registered subjects for the logged-in student
$studentId = $_SESSION['studentId'];
$query = "SELECT subjects.subjectsId, subjects.subjectName, subjects.subjectDay, subjects.subjectSlot 
          FROM registrations 
          INNER JOIN subjects ON registrations.subjectsId = subjects.subjectsId 
          WHERE registrations.studentId='$studentId'";
$registeredSubjectsResult = $conn->query($query);
$registeredSubjectsData = [];

$query = "SELECT * FROM students WHERE studentId='$studentId'";
$studentResult = $conn->query($query);
$studentData = $studentResult->fetch_assoc();

while ($row = $registeredSubjectsResult->fetch_assoc()) {
    $registeredSubjectsData[] = $row;
}

// Handle subject deletion
if (isset($_POST['deleteSubject'])) {
    $deleteSubjectId = $_POST['deleteSubjectId'];

    // Delete the subject from registrations table
    $deleteQuery = "DELETE FROM registrations WHERE studentId='$studentId' AND subjectsId='$deleteSubjectId'";
    if ($conn->query($deleteQuery) === true) {
        $success = "Subject deleted successfully";
    } else {
        $error = "Error deleting subject: " . $conn->error;
    }
}

// Prepare data array
$data = [
    'student' => $studentData,
    'registeredSubjects' => $registeredSubjectsData
];

// Convert data to JSON format
$jsonData = json_encode($data);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buy</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }

        .error-message {
            color: red;
        }

        .success-message {
            color: green;
        }

        .registered-subjects {
            margin-top: 30px;
        }

        .json-data {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buy</h2>
        <?php if (isset($error)) { ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php } elseif (isset($success)) { ?>
            <p class="success-message"><?php echo $success; ?></p>
        <?php } ?>

        <h3>Customer Information</h3>
        <p><strong>Customer ID:</strong> <?php echo $studentData['studentId']; ?></p>
        <p><strong>Name:</strong> <?php echo $studentData['studentName']; ?></p>
        <p><strong>IC:</strong> <?php echo $studentData['studentMatric']; ?></p>
        <p><strong>Age:</strong> <?php echo $studentData['studentAge']; ?></p>

        <form method="POST" action="">
            <div class="form-group">
                <label for="subjectsId">Select Product:</label>
                <select class="form-control" name="subjectsId" required>
                    <option value="" disabled selected>Select a Product</option>
                    <?php
                    // Fetch all subjects from the subjects table
                    $query = "SELECT * FROM subjects";
                    $subjects = $conn->query($query);

                    while ($row = $subjects->fetch_assoc()) {
                        echo "<option value='".$row['subjectsId']."'>".$row['subjectName']."</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Buy</button>
        </form>

        <?php if ($registeredSubjectsResult->num_rows > 0) { ?>
            <div class="registered-subjects">
                <h2>Registered Subjects</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Product Day</th>
                            <th>Product Slot</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reset the result pointer to the beginning
                        $registeredSubjectsResult->data_seek(0);

                        while ($row = $registeredSubjectsResult->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['subjectsId']; ?></td>
                                <td><?php echo $row['subjectName']; ?></td>
                                <td><?php echo $row['subjectDay']; ?></td>
                                <td><?php echo $row['subjectSlot']; ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="deleteSubjectId" value="<?php echo $row['subjectsId']; ?>">
                                        <button type="submit" name="deleteSubject" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
        <a href="index.php" class="btn btn-danger logout-btn">Logout</a>
    </div>
</body>
</html>