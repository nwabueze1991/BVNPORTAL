<?php
function validateInputs($arrayOfInputs, $errors){
  foreach ($arrayOfInputs as $key => $value) {
    switch ($key) {
      case 'email':
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $errors['email'] = 'Invalid Email. Email must be user@example.com.';
        }  elseif (strlen($value) >= 50) {
            $errors['password'] = 'Invalid Email. Email input must be at less than or equals to 50 characters';
        }
        break;
      case 'password':
        if (strlen($value) < 8) {
          $errors['password'] = 'Invalid Password. Password input must be at least 8 characters';
        }
        break;
      case 'text':
        if (strlen($value) < 10) {
          $errors['text'] = 'Text input must be at least 10 characters.';
        } elseif (strlen($value) >= 80) {
          $errors['text'] = 'Text input must not be more than 80 chracters.';
        } elseif (!preg_match("/[a-zA-Z]/", $value)) {
          $errors['text'] = 'Text input must contain letters.';
        }
        break;
      default:
        if (strlen($value) >= 80) {
          $errors['text'] = "$key text input must not be more than 80 chracters.";
        } elseif (strlen($value) === 0) {
          $errors['text'] = "$key text input must not be empty.";
        }
          break;
    }
  }
  return $errors;
}

 ?>
