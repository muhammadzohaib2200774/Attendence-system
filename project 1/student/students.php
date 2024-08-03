<?php
ob_start();
session_start();

// Check if the session is valid
if ($_SESSION['name'] != 'oasis') {
    header('Location: login.php');
    exit();
}

// Establishing connection
include('connect.php');

// Handle form submission
$students_list = '';
if (isset($_POST['sr_btn'])) {
    $srbatch = trim($_POST['sr_batch']);
    
    // Sanitize input
    if (!filter_var($srbatch, FILTER_VALIDATE_INT, array("options" => array("min_range" => 2000, "max_range" => 2099)))) {
        $students_list = "<p class='text-danger'>Please enter a valid batch year (e.g., 2020).</p>";
    } else {
        // Prepare and execute query
        if ($stmt = $conn->prepare("SELECT st_id, st_name, st_dept, st_batch, st_sem, st_email FROM students WHERE st_batch = ? ORDER BY st_id ASC")) {
            $stmt->bind_param("i", $srbatch);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $students_list = '<table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Registration No.</th>
                            <th scope="col">Name</th>
                            <th scope="col">Department</th>
                            <th scope="col">Batch</th>
                            <th scope="col">Semester</th>
                            <th scope="col">Email</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                while ($data = $result->fetch_assoc()) {
                    $students_list .= "<tr>
                        <td>" . htmlspecialchars($data['st_id']) . "</td>
                        <td>" . htmlspecialchars($data['st_name']) . "</td>
                        <td>" . htmlspecialchars($data['st_dept']) . "</td>
                        <td>" . htmlspecialchars($data['st_batch']) . "</td>
                        <td>" . htmlspecialchars($data['st_sem']) . "</td>
                        <td>" . htmlspecialchars($data['st_email']) . "</td>
                    </tr>";
                }
                
                $students_list .= '</tbody></table>';
            } else {
                $students_list = "<p class='text-warning'>No students found for the provided batch.</p>";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Online Attendance Management System 1.0</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
    <header>
        <h1>Online Attendance Management System 1.0</h1>
        <div class="navbar">
            <a href="index.php">Home</a>
            <a href="students.php">Students</a>
            <a href="report.php">My Report</a>
            <a href="account.php">My Account</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <center>
        <div class="row">
            <div class="content">
                <h3>Student List</h3>
                <br>
                <form method="post" action="" class="form-horizontal col-md-6 col-md-offset-3">
                    <div class="form-group">
                        <label for="input1" class="col-sm-3 control-label">Batch</label>
                        <div class="col-sm-7">
                            <input type="text" name="sr_batch" class="form-control" id="input1" placeholder="Only 2020" required />
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary col-md-3 col-md-offset-7" value="Go!" name="sr_btn" />
                </form>

                <div class="content"></div>
                <?php echo $students_list; ?>
            </div>
        </div>
    </center>
</body>
</html>
