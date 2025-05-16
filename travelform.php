<?php
// include "partials/navigation.php";
include "functions.php";
session_start();

$insert = false;
if (isset($_POST['submit'])){  //$_SERVER["REQUEST_METHOD"] == "POST"
    // Set connection variables
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "trip";
    // Create a database connection
    $conn = mysqli_connect($server, $username, $password, $dbname);

    // Check for connection success
    if(!$conn){
        die("connection to this database failed due to" . mysqli_connect_error());
    }
    
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $hasError = false;
    $nameErr = $ageErr = $emailErr = $phoneErr = "";
    $name = $age = $email = $phone = "";

    $name = test_input($_POST['name']);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $nameErr = "Only letters and white space allowed";
        $hasError = true;
    }
    $gender = test_input($_POST['gender']);
    $age = test_input($_POST['age']);
    if ($age < 18 || $age > 150) {
        $ageErr = "Sorry, you cannot register for the trip.";
        $hasError = true;
    }
    $email = test_input($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $hasError = true;
    }
    $phone = test_input($_POST['phone']);
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $phoneErr = "Invalid Phone Number";
        $hasError = true;
    }
    $desc = test_input($_POST['desc']);
    if (!$hasError) {
        $sql = "INSERT INTO `trip`.`trip` (`name`, `age`, `gender`, `email`, `phone`, `other`, `dt`) VALUES ('$name', '$age', '$gender', '$email', '$phone', '$desc', current_timestamp());";
        if (mysqli_query($conn, $sql)) {
            $insert = true;
        } else {
            echo "ERROR: $sql <br>" . mysqli_error($conn);
        }
    }
    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Travel Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?php echo getPageClass()?>">
    <nav>
        <ul>
            <li>
                <a class="<?php echo setActiveCLass('index.php');  ?>"  href="index.php">Home</a>
            </li>

            <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <li>
                    <a class="<?php echo setActiveCLass('admin.php'); ?>" href="admin.php">Admin</a>
                </li>
                <li>
                    <a href="logout.php">Logout</a>
                </li>
                <li>
                    <a class="<?php echo setActiveCLass('travelform.php'); ?>" href="travelform.php">Form</a>
                </li>
            <?php else: ?>
                <li>
                    <a class="<?php echo setActiveCLass('register.php');  ?>"  href="register.php">Register</a>
                </li>
                <li>
                    <a class="<?php echo setActiveCLass('login.php');  ?>" href="login.php">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="container">
        <div class="form-container">
            <h1>Welcome to Image Online Trip form</h3>
            <p>Enter your details and submit this form to confirm your participation in the trip </p>
            <p>Today's date is <?php echo date("Y-m-d"); ?>, Please fill the form before <?php echo date("Y-m-d",strtotime("May 31 2025 23:59:59"));?> </p>
            <?php
                if($insert == true){
                echo "<p class='submitMsg'>Thanks for submitting your form. We are happy to see you joining us for the US trip</p>";
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="text" name="name" id="name" placeholder="Enter your name" required>
                    <span class="error-message" id="name-error"><?php echo $nameErr ?? ''; ?></span>
                    <input type="text" name="age" id="age" placeholder="Enter your Age" required>
                    <span class="error-message" id="age-error"><?php echo $ageErr ?? ''; ?></span>
                    <select name="gender" id="gender" required>
                        <option value="" disabled selected>Select your gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="others">Others</option>
                    </select>
                    <span class="error-message" id="gender-error"></span>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                    <span class="error-message" id="email-error"><?php echo $emailErr ?? ''; ?></span>
                    <input type="phone" name="phone" id="phone" placeholder="Enter your phone" required>
                    <span class="error-message" id="phone-error"></span>
                    <span class="error-message" id="desc-error"><?php echo $phoneErr ?? ''; ?></span>
                    <textarea name="desc" id="desc" cols="30" rows="10" placeholder="Enter any other information here"></textarea>
                    
                    <button class="btn" name="submit">Submit</button> 
                </form>
                <form method="post">
                    <button class="btn" name="show_names">Show Registered Users</button>
                </form>
                                
                <?php
                if (isset($_POST['show_names'])) {
                    // DB connection
                    $servername = "localhost";
                    $username = "root";
                    $password = ""; // Default for XAMPP
                    $database = "trip";

                    // Create connection
                    $conn = mysqli_connect($servername, $username, $password, $database);

                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }

                    // Fetch names
                    $sql = "SELECT name FROM trip";
                    $result = mysqli_query($conn, $sql);

                    echo "<div class='container'>";
                    echo "Total Registered Users:  " . mysqli_num_rows($result);
                    echo "<h2>Registered Users:</h2>";
                    echo "<ol>";

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<li>" . htmlspecialchars($row['name']) . "</li>";
                        }
                    } else {
                        echo "<li>No users registered yet.</li>";
                    }

                    echo "</ol>";
                    echo "</div>";

                    mysqli_close($conn);
                }
            ?>
        </div>
    </div>
    
    <script src="index.js"></script>
    
</body>
</html>
