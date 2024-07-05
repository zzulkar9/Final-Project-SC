<?php
$mysqli = new mysqli("localhost", "root", "Cloudhosting123@", "ass3");
if ($mysqli->connect_error) {
    exit('Could not connect');
}

$sqlStudent = "SELECT studentId, studentName, studentMatric, studentAge
               FROM students WHERE studentId = ?";

$stmtStudent = $mysqli->prepare($sqlStudent);
$stmtStudent->bind_param("s", $_GET['q']);
$stmtStudent->execute();
$stmtStudent->store_result();
$stmtStudent->bind_result($studentId, $studentName, $studentMatric, $studentAge);
$stmtStudent->fetch();
$stmtStudent->close();

$sqlSubjects = "SELECT subjects.subjectsId, subjects.subjectName, subjects.subjectDay, subjects.subjectSlot
                FROM registrations
                INNER JOIN subjects ON registrations.subjectsId = subjects.subjectsId
                WHERE registrations.studentId = ?";

$stmtSubjects = $mysqli->prepare($sqlSubjects);
$stmtSubjects->bind_param("s", $_GET['q']);
$stmtSubjects->execute();
$stmtSubjects->store_result();
$stmtSubjects->bind_result($subjectsId, $subjectName, $subjectDay, $subjectSlot);

$registeredSubjectsData = [];
while ($stmtSubjects->fetch()) {
    $registeredSubjectsData[] = [
        'productId' => $subjectsId,
        'subjectName' => $subjectName,
        'subjectDay' => $subjectDay,
        'subjectSlot' => $subjectSlot
    ];
}

$data = [
    'cusomer' => [
        'customerId' => $studentId,
        'customerName' => $studentName,
        'customerMatric' => $studentMatric,
        'customerAge' => $studentAge
    ],
    'registeredProducts' => $registeredSubjectsData
];

$jsonData = json_encode($data, JSON_PRETTY_PRINT);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }

        table {
            width: 100%;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Customer's Information</h2>
        <table>
            <tr>
                <th>Customer ID</th>
                <td><?php echo $studentId; ?></td>
                <th>Customer Name</th>
                <td><?php echo $studentName; ?></td>
            </tr>
            <tr>
                <th>Customer Matric</th>
                <td><?php echo $studentMatric; ?></td>
                <th>Customer Age</th>
                <td><?php echo $studentAge; ?></td>
            </tr>
        </table>

        <h2>Products Bought</h2>
        <?php if (!empty($registeredSubjectsData)) { ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Product Description</th>
                    <th>Product Price</th>
                </tr>
                <?php foreach ($registeredSubjectsData as $subject) { ?>
                    <tr>
                    
                        <td><?php echo $subject['subjectName']; ?></td>
                        <td><?php echo $subject['subjectDay']; ?></td>
                        <td><?php echo $subject['subjectSlot']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No registered subjects.</p>
        <?php } ?>

        <h2>JSON Data</h2>
        <pre><?php echo $jsonData; ?></pre>
    </div>
</body>
</html>