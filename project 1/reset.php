<?php 
include('connect.php'); // Ensure this file uses mysqli

// Check if form is submitted
if (isset($_POST['reset'])) {
    $email = trim($_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<p class='text-danger'>Please enter a valid email address.</p>";
    } else {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT password FROM admininfo WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $message = "<p class='text-warning'>Email is not associated with any account. Contact OAMS 1.0.</p>";
        } else {
            $data = $result->fetch_assoc();
            $message = "<strong><p style='text-align: left;'>Hi there!<br>You requested a password recovery. You may <a href='index.php'>Login here</a> and enter this key as your password to login. Recovery key: <mark>" . htmlspecialchars($data['password']) . "</mark><br>Regards,<br>Online Attendance Management System 1.0</p></strong>";
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
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<header>
    <h1>Online Attendance Management System 1.0</h1>
    <div class="navbar">
        <a href="index.php">Login</a>
    </div>
</header>

<center>
    <div class="content">
        <div class="row">
            <form method="post" class="form-horizontal col-md-6 col-md-offset-3">
                <h3>Recover Your Password</h3>

                <div class="form-group">
                    <label for="input1" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" name="email" class="form-control" id="input1" placeholder="your email" required />
                    </div>
                </div>

                <input type="submit" class="btn btn-primary col-md-2 col-md-offset-10" value="Go" name="reset" />
            </form>

            <br>

            <?php if (isset($message)) echo $message; ?>
        </div>
    </div>
</center>

</body>
</html>
