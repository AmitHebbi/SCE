<?php
include_once 'resource/Database.php';
/**
 * @param $required_fields_array , n array containing the list of all required fields
 * @return array, containing all errors
 */
function check_empty_fields($required_fields_array)
{
    //initialize an array to store error messages
    $form_errors = array();

    //loop through the required fields array snd popular the form error array
    foreach ($required_fields_array as $name_of_field) {
        if (!isset($_POST[$name_of_field]) || $_POST[$name_of_field] == NULL) {
            $form_errors[] = $name_of_field ." is required "."<br>";
        }
    }

    return $form_errors;
}

/**
 * @param $fields_to_check_length , an array containing the name of fields
 * for which we want to check min required length e.g array('username' => 4, 'email' => 12)
 * @return array, containing all errors
 */
function check_min_length($fields_to_check_length)
{
    //initialize an array to store error messages
    $form_errors = array();

    foreach ($fields_to_check_length as $name_of_field => $minimum_length_required) {
        if (strlen(trim($_POST[$name_of_field])) < $minimum_length_required) {
            $form_errors[] = $name_of_field . " is too short, must be {$minimum_length_required} characters long"."<br>";
        }
    }
    return $form_errors;
}

/**
 * @param $data , store a key/value pair array where key is the name of the form control
 * in this case 'email' and value is the input entered by the user
 * @return array, containing email error
 */
function check_email($data)
{
    //initialize an array to store error messages
    $form_errors = array();
    $key = 'email';
    //check if the key email exist in data array
    if (array_key_exists($key, $data)) {

        //check if the email field has a value
        if ($_POST[$key] != null) {

            // Remove all illegal characters from email
            $key = filter_var($key, FILTER_SANITIZE_EMAIL);

            //check if input is a valid email address
            if (filter_var($_POST[$key], FILTER_VALIDATE_EMAIL) === false) {
                $form_errors[] = $key . " is not a valid email address";
            }
        }
    }
    return $form_errors;
}

/**
 * @param $form_errors_array , the array holding all
 * errors which we want to loop through
 * @return string, list containing all error messages
 */
function show_errors($form_errors_array)
{
    $errors = "";
    //loop through error array and display all items in a list
    foreach ($form_errors_array as $the_error) {
        $errors .= "{$the_error}";
    }

    return $errors;
}

/**
 * @param $message, message to display
 * @param string $passOrFail, test condition to determine message type
 * @return string, returns the message
 */

function flashMessage($message, $passOrFail = "Fail")
{
    if ($passOrFail == "Pass") {
        $data = "{$message}";
    } else {
        $data = "{$message}";
    }

    return $data;
}

/**
 * @param $page, redirect user to page specified
 */

function redirectTo($page){
    header("Location: {$page}.php");
}

/**
 * @param $table, table that we want to search
 * @param $column_name, the column name
 * @param $value, the data collected from the form
 * @param $db, database object
 * @return bool, returns true if record exist else false
 */

function checkDuplicateEntries($table, $column_name, $value, $db){
    try{
        $sqlQuery = "SELECT * FROM $table WHERE $column_name=:$column_name";
        $statement = $db->prepare($sqlQuery);
        $statement->execute(array(":$column_name" => $value));

        if($row = $statement->fetch()){
            return true;
        }
        return false;
    }catch (PDOException $ex){
        //handle exception
    }
}


/**
 * kill all sessions, cookies and regenerate session ID
 * Redirect to index page after all
 */
function signout(){
    unset($_SESSION['username']);
    unset($_SESSION['id']);

    session_destroy();
    session_regenerate_id(true);
    redirectTo('login');
}