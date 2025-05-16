<?php
include "partials/header.php";
include "partials/navigation.php";

if(is_user_logged_in()){
    header("Location: admin.php");
    exit;
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
        if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
        }
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

// Check if the password and confirm match
    if($password !== $confirm_password){
        $error =  "Password do not match";
    } else {

        // check ig username already exists
        if(user_exists($conn, $username)){
            $error = "Username already exists, Please choose another";
        } else {

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$passwordHash', '$email')";

            if(mysqli_query($conn, $sql)){
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                redirect("admin.php");
                exit;
            }else {
                $error =  "SOMETHING HAPPENED not data inserted, error: " . mysqli_error($conn);
            };


        }

    }

}

?>

<div class="container">
<div class="form-container">

<form method="POST" action="" id="registerForm">
    <h2>Create your Account</h2>

    <?php if($error): ?>
        <p style="color:red">
            <?php echo $error; ?>
        </p>
    <?php endif; ?>

    <label for="username">Username:</label>
    <input value="<?php echo isset($username) ? $username : ''; ?>" placeholder="Enter your username" type="text" name="username" id="username" required>
    <div class="error" id="usernameError" style="color:red;"></div>

    <label for="email">Email:</label>
    <input value="<?php echo isset($email) ? $email : ''; ?>" placeholder="Enter your email" type="email" name="email" id="email" required>
    <div class="error" id="emailError" style="color:red;"></div>

    <label for="password">Password:</label>
    <input placeholder="Enter your password" type="password" name="password" id="password" required>
    <div class="error" id="passwordError" style="color:red;"></div>

    <label for="confirm_password">Confirm Password:</label>
    <input placeholder="Confirm your password" type="password" name="confirm_password" id="confirm_password" required>
    <div class="error" id="confirmPasswordError" style="color:red;"></div>

    <input type="submit" value="Register">
</form>
</div>
</div>

<script>
document.getElementById("registerForm").addEventListener("submit", function(event) {
    let isValid = true;

    // Clear previous error messages
    document.querySelectorAll(".error").forEach(el => el.textContent = "");

    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    // Username: 4-20 characters, letters/numbers/underscores
    const usernameRegex = /^[a-zA-Z0-9_]{4,20}$/;
    if (!usernameRegex.test(username)) {
        document.getElementById("usernameError").textContent = "Username must be 4–20 characters, only letters, numbers, or underscores.";
        isValid = false;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        document.getElementById("emailError").textContent = "Please enter a valid email address.";
        isValid = false;
    }

    // Password length check
    if (password.length < 8) {
        document.getElementById("passwordError").textContent = "Password must be at least 8 characters.";
        isValid = false;
    }

    // Confirm password match
    if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").textContent = "Passwords do not match.";
        isValid = false;
    }

    // Prevent form submit if any validation failed
    if (!isValid) {
        event.preventDefault();
    }
});
</script>
<?php include "partials/footer.php"; ?>

<?php
mysqli_close($conn);
?>