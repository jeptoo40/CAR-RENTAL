<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: Database connection using PDO
$host = "localhost";
$dbname = "user_system";
$username = "root";  // Your MySQL username
$password = "1234";  // Your MySQL password

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Step 2: Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Step 3: Collect form data and sanitize it
    $fullname = htmlspecialchars($_POST['fullname']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit();
    }

    // Validate password strength
    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters!";
        exit();
    }

    // Step 4: Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Step 5: Prepare and execute the SQL query
    $sql = "INSERT INTO users (fullname, username, email, phone, password) 
            VALUES (:fullname, :username, :email, :phone, :password)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashed_password);
        
        // Step 6: Execute the query
        if ($stmt->execute()) {
            // Optional: display a success message
            echo "Registration successful! Redirecting to login...";
            
            // Redirect to login.php after 2 seconds
            header("refresh:2;url=login.html");
            exit();
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
        
    } catch(PDOException $e) {
        // Check for duplicate entry error
        if ($e->getCode() == 23000) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                // Provide a user-friendly message
                if (strpos($e->getMessage(), 'username') !== false) {
                    echo "The username is already taken. Please choose a different one.";
                } elseif (strpos($e->getMessage(), 'email') !== false) {
                    echo "The email is already in use. Please choose a different one.";
                } else {
                    echo "An error occurred. Please try again later.";
                }
            }
        } else {
            // General error message for other issues
            echo "An error occurred. Please try again later.";
        }
    }
}
?>
