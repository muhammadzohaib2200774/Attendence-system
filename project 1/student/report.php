<?php
ob_start();
session_start();

// Checking if the session is valid
if ($_SESSION['name'] != 'oasis') {
    header('Location: login.php');
    exit();
}

// Establishing connection
include('connect.php');

// Handle form submission
$success_msg = $error_msg = '';
if (isset($_POST['sr_btn'])) {
    $sr_id = trim($_POST['sr_id']);
    $course = trim($_POST['whichcourse']);

    if (empty($sr_id) || empty($course)) {
        $error_msg = "Please provide both registration number and course.";
    } else {
        // Prepare and execute queries
        $stmt = $conn->prepare("SELECT COUNT(*) AS countP FROM attendance WHERE stat_id = ? AND course = ? AND st_status = 'Present'");
        $stmt->bind_param("ss", $sr_id, $course);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $count_pre = $data['countP'];

        $stmt = $conn->prepare("SELECT COUNT(*) AS countT FROM attendance WHERE stat_id = ? AND course = ?");
        $stmt->bind_param("ss", $sr_id, $course);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $count_tot = $data['countT'];

        if ($count_tot > 0) {
            $success_msg = "
                <table class='table table-striped'>
                    <tbody>
                        <tr>
                            <td>Registration No.:</td>
                            <td>" . htmlspecialchars($sr_id) . "</td>
                        </tr>
                        <tr>
                            <td>Total Class (Days):</td>
                            <td>" . htmlspecialchars($count_tot) . "</td>
                        </tr>
                        <tr>
                            <td>Present (Days):</td>
                            <td>" . htmlspecialchars($count_pre) . "</td>
                        </tr>
                        <tr>
                            <td>Absent (Days):</td>
                            <td>" . htmlspecialchars($count_tot - $count_pre) . "</td>
                        </tr>
                    </tbody>
                </table>";
        } else {
            $error_msg = "No records found for the provided ID and course.";
        }

        $stmt->close();
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
                <h3>Student Report</h3>
                <br>

                <!-- Error or Success Message printing -->
                <p>
                    <?php
                    if ($success_msg) {
                        echo $success_msg;
                    }
                    if ($error_msg) {
                        echo $error_msg;
                    }
                    ?>
                </p>
                <!-- Error or Success Message printing ended -->

                <br>
                <form method="post" action="" class="form-horizontal col-md-6 col-md-offset-3">
                    <div class="form-group">
                        <label for="input1" class="col-sm-3 control-label">Select Subject</label>
                        <div class="col-sm-4">
                            <select name="whichcourse" id="input1" class="form-control" required>
                                <option value="algo">Analysis of Algorithms</option>
                                <option value="algolab">Analysis of Algorithms Lab</option>
                                <option value="dbms">Database Management System</option>
                                <option value="dbmslab">Database Management System Lab</option>
                                <option value="weblab">Web Programming Lab</option>
                                <option value="os">Operating System</option>
                                <option value="oslab">Operating System Lab</option>
                                <option value="obm">Object Based Modeling</option>
                                <option value="softcomp">Soft Computing</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input1" class="col-sm-3 control-label">Your Reg. No.</label>
                        <div class="col-sm-7">
                            <input type="text" name="sr_id" class="form-control" id="input1" placeholder="Enter your reg. no." required />
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary col-md-3 col-md-offset-7" value="Go!" name="sr_btn" />
                </form>
            </div>
        </div>
    </center>
</body>
</html>
