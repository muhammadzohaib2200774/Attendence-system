<?php
ob_start();
session_start();

// Checking if the session is valid
if ($_SESSION['name'] != 'oasis') {
    header('Location: ../login.php');
    exit();
}

// Establishing connection
include('connect.php');

try {
    // Checking form data and empty fields
    if (isset($_POST['done'])) {
        if (empty($_POST['name'])) {
            throw new Exception("Name cannot be empty.");
        }
        if (empty($_POST['dept'])) {
            throw new Exception("Department cannot be empty.");
        }
        if (empty($_POST['batch'])) {
            throw new Exception("Batch cannot be empty.");
        }
        if (empty($_POST['email'])) {
            throw new Exception("Email cannot be empty.");
        }

        // Initializing the student id
        $sid = $_POST['id'];

        // Updating student's information in the database table "students"
        $stmt = $conn->prepare("UPDATE students SET st_name=?, st_dept=?, st_batch=?, st_sem=?, st_email=? WHERE st_id=?");
        $stmt->bind_param("sssssi", $_POST['name'], $_POST['dept'], $_POST['batch'], $_POST['semester'], $_POST['email'], $sid);
        
        if ($stmt->execute()) {
            $success_msg = 'Updated successfully';
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $error_msg = $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Online Attendance Management System 1.0</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="styles.css">
    <!-- Latest compiled and minified JavaScript -->
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
                <h3>Update Account</h3>
                <br>

                <!-- Error or Success Message printing -->
                <p>
                    <?php
                    if (isset($success_msg)) {
                        echo $success_msg;
                    }
                    if (isset($error_msg)) {
                        echo $error_msg;
                    }
                    ?>
                </p>
                <!-- Error or Success Message printing ended -->

                <br>

                <!-- Search form -->
                <form method="post" action="" class="form-horizontal col-md-6 col-md-offset-3">
                    <div class="form-group">
                        <label for="sr_id" class="col-sm-3 control-label">Registration No.</label>
                        <div class="col-sm-7">
                            <input type="text" name="sr_id" class="form-control" id="sr_id" placeholder="Enter your reg. no. to continue" required />
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary col-md-3 col-md-offset-7" value="Go!" name="sr_btn" />
                </form>
                <div class="content"></div>

                <?php
                if (isset($_POST['sr_btn'])) {
                    // Initializing student ID from form data
                    $sr_id = $_POST['sr_id'];

                    // Searching student's information by ID
                    $stmt = $conn->prepare("SELECT * FROM students WHERE st_id=?");
                    $stmt->bind_param("s", $sr_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($data = $result->fetch_assoc()) {
                            ?>
                            <form action="" method="post" class="form-horizontal col-md-6 col-md-offset-3">
                                <table class="table table-striped">
                                    <tr>
                                        <td>Registration No.:</td>
                                        <td><?php echo htmlspecialchars($data['st_id']); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Student's Name:</td>
                                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($data['st_name']); ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <td>Department:</td>
                                        <td><input type="text" name="dept" value="<?php echo htmlspecialchars($data['st_dept']); ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <td>Batch:</td>
                                        <td><input type="text" name="batch" value="<?php echo htmlspecialchars($data['st_batch']); ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <td>Semester:</td>
                                        <td><input type="text" name="semester" value="<?php echo htmlspecialchars($data['st_sem']); ?>" required /></td>
                                    </tr>
                                    <tr>
                                        <td>Email:</td>
                                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($data['st_email']); ?>" required /></td>
                                    </tr>
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($sr_id); ?>" />
                                    <tr>
                                        <td></td>
                                        <td><input type="submit" class="btn btn-primary col-md-3 col-md-offset-7" value="Update" name="done" /></td>
                                    </tr>
                                </table>
                            </form>
                            <?php
                        }
                    } else {
                        echo '<p>No student found with this ID.</p>';
                    }
                    $stmt->close();
                }
                ?>
            </div>
        </div>
    </center>
</body>
</html>
