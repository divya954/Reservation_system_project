<?php
require_once "config.php";

// Initialize variables
$username = $password = $business_phone = $business_address = "";
$username_err = $password_err = $business_phone_err = $business_address_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } else {
        $password = cleanInput($_POST["password"]);
    }

    // Validate business phone
    if (empty(trim($_POST["business_phone"]))) {
        $business_phone_err = "Please enter a business phone number.";
    } else {
        $business_phone = cleanInput($_POST["business_phone"]);
    }

    // Validate business address
    if (empty(trim($_POST["business_address"]))) {
        $business_address_err = "Please enter a business address.";
    } else {
        $business_address = cleanInput($_POST["business_address"]);
    }

    // Check input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($business_phone_err) && empty($business_address_err)) {
        // Insert data into users table
        $sql_user = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
        if ($stmt_user = $link->prepare($sql_user)) {
            $user_type = "lister"; // set default user type
            $stmt_user->bind_param("sss", $param_username, $param_password, $param_user_type);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_user_type = $user_type;
            if ($stmt_user->execute()) {
                // Get the inserted user's id
                $user_id = $stmt_user->insert_id;

                // Insert data into venue_lister table
                $sql_lister = "INSERT INTO venue_lister (business_phone_number, business_address, id) VALUES (?, ?, ?)";
                if ($stmt_lister = $link->prepare($sql_lister)) {
                    $stmt_lister->bind_param("ssi", $param_business_phone, $param_business_address, $param_id);
                    $param_business_phone = $business_phone;
                    $param_business_address = $business_address;
                    $param_id = $user_id;

                    if ($stmt_lister->execute()) {
                        header("location: index.php");
                    } else {
                        echo "Something went wrong. Please try again later.";
                    }
                    $stmt_lister->close();
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt_user->close();
        }
    }
    $link->close();
}

function cleanInput($input)
{
    return htmlspecialchars(stripslashes(trim($input)));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lister Signup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Lister Signup</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <span class="text-danger"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="text-danger"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label for="business_phone">Business Phone:</label>
                <input type="tel" class="form-control" id="business_phone" name="business_phone" required>
                <span class="text-danger"><?php echo $business_phone_err; ?></span>
            </div>
            <div class="form-group">
                <label for="business_address">Business Address:</label>
                <input type="text" class="form-control" id="business_address" name="business_address" required>
                <span class="text-danger"><?php echo $business_address_err; ?></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
            </div>
            <p class="text-center">Already have an account? <a href="index.php">Login here</a>.</p>
        </form>
    </div>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>
