<?php
//add our database connection script
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

//process the form
if (isset($_POST['signupBtn'])) {
    //initialize an array to store any error message from the form
    $form_errors = array();

    //Form validation
    $required_fields = array('email', 'username', 'password');

    //call the function to check empty field and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    //Fields that requires checking for minimum length
    $fields_to_check_length = array('username' => 10, 'password' => 6);

    //call the function to check minimum required length and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));

    //email validation / merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_email($_POST));

    //collect form data and store in variables
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (checkDuplicateEntries("users", "email", $email, $db)) {
        $result = flashMessage("Email is already taken, please try another one");
    } else if (checkDuplicateEntries("users", "username", $username, $db)) {
        $result = flashMessage("USN is already taken, please try another one");
    } //check if error array is empty, if yes process form data and insert record
    else if (empty($form_errors)) {
        //hashing the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {

            //create SQL insert statement
            $sqlInsert = "INSERT INTO users (username, email, password, join_date)
              VALUES (:username, :email, :password, now())";

            //use PDO prepared to sanitize data
            $statement = $db->prepare($sqlInsert);

            //add the data into the database
            $statement->execute(array(':username' => $username, ':email' => $email, ':password' => $hashed_password));

            //check if one new row was created
            if ($statement->rowCount() == 1) {
                $result = flashMessage("Registration Successful", "Pass");
            }
        } catch (PDOException $ex) {
            $result = flashMessage("An error occurred" . $ex->getMessage());
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        SCE Portal
    </title>
    <!-- Favicon -->
    <link href="assets/img/brand/favicon.png" rel="icon" type="image/png">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Icons -->
    <link href="assets/js/plugins/nucleo/css/nucleo.css" rel="stylesheet"/>
    <link href="assets/js/plugins/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet"/>
    <!-- CSS Files -->
    <link href="assets/css/dashboard.css?v=1.1.0" rel="stylesheet"/>
</head>


<body class="bg-default">
<form method="post" action="">
    <div class="main-content">
        <!-- Header -->
        <div class="header bg-gradient-primary py-7 py-lg-8">
            <div class="container">
                <div class="header-body text-center mb-7">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-6">
                            <h1 class="text-white">Welcome!</h1>
                            <p class="text-lead text-light">Bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla
                                bla
                                bla bla bla.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator separator-bottom separator-skew zindex-100">
                <svg x="0" y="0" viewBox="0 0 2560 100" preserveAspectRatio="none" version="1.1"
                     xmlns="http://www.w3.org/2000/svg">
                    <polygon class="fill-default" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </div>
        <!-- Page content -->
        <div class="container mt--8 pb-5">
            <!-- Table -->
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card bg-secondary shadow border-0">
                        <div class="card-body px-lg-5 py-lg-5">
                            <div class="card-body px-lg-5 py-lg-5">
                                <?php if (isset($result)) : ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <span class="alert-inner--text"><?php echo $result; ?></span>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($form_errors)) : ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <span class="alert-inner--text"><?php echo show_errors($form_errors); ?></span>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <form role="form">
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="USN" type="text" value=""
                                                   name="username">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="Email" type="text" value=""
                                                   name="email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                            class="ni ni-lock-circle-open"></i></span>
                                            </div>
                                            <input class="form-control" placeholder="Password" type="password" value=""
                                                   name="password">
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" value="Signup" name="signupBtn"
                                                class="btn btn-primary mt-4">
                                            Create account
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p class="text-light">
                                <small>Already have an account? <a href="login.php" class="text-light">Log In</a></small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <footer class="py-5">
                <div class="container">
                    <div class="row align-items-center justify-content-xl-between">
                        <div class="col-xl-6">
                            <div class="copyright text-center text-xl-left text-muted">
                                &copy; 2019 <a href="#" class="font-weight-bold ml-1" target="_blank">SCEPortal</a>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <ul class="nav nav-footer justify-content-center justify-content-xl-end">
                                <li class="nav-item">
                                    <a href="#" class="nav-link" target="_blank">Amit Hebbi</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" target="_blank">About Us</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" target="_blank">GitHub</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
</form>
<!--   Core   -->
<script src="assets/js/plugins/jquery/dist/jquery.min.js"></script>
<script src="assets/js/plugins/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!--   Optional JS   -->
<!--   Dashboard JS   -->
<script src="assets/js/dashboard.min.js?v=1.1.0"></script>
</body>

</html>