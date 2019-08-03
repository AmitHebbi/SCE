<?php
//add our database connection script
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

//process the form if the reset password button is clicked
if (isset($_POST['passwordResetBtn'])) {
    //initialize an array to store any error message from the form
    $form_errors = array();

    //Form validation
    $required_fields = array('email', 'new_password', 'confirm_password');

    //call the function to check empty field and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    //Fields that requires checking for minimum length
    $fields_to_check_length = array('new_password' => 6, 'confirm_password' => 6);

    //call the function to check minimum required length and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));

    //email validation / merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_email($_POST));

    //check if error array is empty, if yes process form data and insert record
    if (empty($form_errors)) {
        //collect form data and store in variables
        $email = $_POST['email'];
        $password1 = $_POST['new_password'];
        $password2 = $_POST['confirm_password'];

        //check if new password and confirm password is same
        if ($password1 != $password2) {
            $result = "<p style='padding:20px; border: 1px solid gray; color: red;'> New password and confirm password does not match</p>";
        } else {
            try {
                //create SQL select statement to verify if email address input exist in the database
                $sqlQuery = "SELECT email FROM users WHERE email =:email";

                //use PDO prepared to sanitize data
                $statement = $db->prepare($sqlQuery);

                //execute the query
                $statement->execute(array(':email' => $email));

                //check if record exist
                if ($statement->rowCount() == 1) {
                    //hash the password
                    $hashed_password = password_hash($password1, PASSWORD_DEFAULT);

                    //SQL statement to update password
                    $sqlUpdate = "UPDATE users SET password =:password WHERE email=:email";

                    //use PDO prepared to sanitize SQL statement
                    $statement = $db->prepare($sqlUpdate);

                    //execute the statement
                    $statement->execute(array(':password' => $hashed_password, ':email' => $email));

                    $result = "<p style='padding:20px; border: 1px solid gray; color: green;'> Password Reset Successful</p>";
                } else {
                    $result = "<p style='padding:20px; border: 1px solid gray; color: red;'> The email address provided
                                does not exist in our database, please try again</p>";
                }
            } catch (PDOException $ex) {
                $result = "<p style='padding:20px; border: 1px solid gray; color: red;'> An error occurred: " . $ex->getMessage() . "</p>";
            }
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
                            <h1 class="text-white">Recover Password</h1>
                            <p class="text-lead text-light">Enter your Email and instructions will be sent to you!</p>
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
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card bg-secondary shadow border-0">
                        <div class="card-body px-lg-5 py-lg-5">
                            <?php if(isset($result)) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-inner--text"><?php echo $result; ?></span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($form_errors)) :  ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="alert-inner--text"><?php echo show_errors($form_errors); ?></span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <form role="form">
                                <div class="form-group mb-3">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                        </div>
                                        <input class="form-control" placeholder="Email" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                        </div>
                                        <input class="form-control" placeholder="New Password" type="password" value=""
                                               name="new_password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group input-group-alternative">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                        </div>
                                        <input class="form-control" placeholder="Confirm Password" type="password"
                                               value="" name="confirm_password">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" value="Reset Password" name="passwordResetBtn"
                                            class="btn btn-primary my-4">Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="py-5">
            <div class="container">
                <div class="row align-items-center justify-content-xl-between">
                    <div class="col-xl-6">
                        <div class="copyright text-center text-xl-left text-muted">
                            © 2019 <a href="#" class="font-weight-bold ml-1" target="_blank">SCEPortal</a>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <ul class="nav nav-footer justify-content-center justify-content-xl-end">
                            <li class="nav-item">
                                <a href="" class="nav-link" target="_blank">Amit Hebbi</a>
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
    </div>
</form>
<!--   Core   -->
<script src="assets/js/plugins/jquery/dist/jquery.min.js"></script>
<script src="assets/js/plugins/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!--   Optional JS   -->
<!--   Dashboard JS   -->
<script src="assets/js/dashboard.min.js?v=1.1.0"></script>
</body>

</html>