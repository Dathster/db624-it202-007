<?php
//db624 it202-007 11/11/24
function sanitize_email($email = "")
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "")
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
function is_valid_username($username)
{
    return preg_match('/^[a-z0-9_-]{3,16}$/', $username);
}
function is_valid_password($password)
{
    return strlen($password) >= 8;
}

function validate_numbers($input){
    return is_numeric($input) && $input > 0;
}

function validateDateFormat($date, $format = 'Y-m-d') {
    $dateTime = DateTime::createFromFormat($format, $date);
    
    // Check if the date matches the format and is a valid date
    return $dateTime && $dateTime->format($format) === $date;
}

