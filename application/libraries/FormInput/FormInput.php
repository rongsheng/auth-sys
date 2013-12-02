<?php

/**
 * Handle inputs from forms.
 *
 * Author: Anindha Parthy
 * Created: 26.05.2010
 *
 * Modified: Feb 2013 - Luke Anderson:
 *    - Implemented Validation (now is argument to get[Get|Post]Input functions)
 *    - Removed cleaning (unless explicitly specified in argument)
 */
require_once('Form_validation.php');

class ValidationException extends InvalidArgumentException {
    protected $errors;
    public function __construct($message, $code = 0, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
    public function getErrors() {
        return $this->errors;
    }
    public function setErrors($newValue) {
        $this->errors = $newValue;
    }
}

// Translate UNICODE chars to their approximate latin1 character
function convert_from_unicode($val) {
    // if $val is not string, the following function will output a warning. Jun is handling it.
    $trans = iconv("utf-8", "latin1//TRANSLIT", $val);
    // If that didn't work because it wasn't UTF-8 and got truncated on an unrecognised character
    if (mb_strlen($trans) < mb_strlen($val, 'utf-8')) {
        // Just remove all unrecognised characters
        $trans = iconv("UTF-8", "latin1//IGNORE", $val);
    }
    return iconv("latin1", "UTF-8", $trans);
}

class FormInput {
    public static $error_msgs = array(
        "required" => "The %s field is required.",
        "isset" => "The %s field must have a value.",
        "valid_email" => "The %s field must contain a valid email address.",
        "valid_emails" => "The %s field must contain all valid email addresses.",
        "valid_url" => "The %s field must contain a valid URL.",
        "valid_ip" => "The %s field must contain a valid IP.",
        "min_length" => "The %s field must be at least %s characters in length.",
        "max_length" => "The %s field can not exceed %s characters in length.",
        "exact_length" => "The %s field must be exactly %s characters in length.",
        "alpha" => "The %s field may only contain alphabetical characters.",
        "alpha_numeric" => "The %s field may only contain alpha-numeric characters.",
        "alpha_dash" => "The %s field may only contain alpha-numeric characters, underscores, and dashes.",
        "numeric" => "The %s field must contain only numbers.",
        "is_numeric" => "The %s field must contain only numeric characters.",
        "integer" => "The %s field must contain an integer.",
        "regex_match" => "The %s field is not in the correct format.",
        "matches" => "The %s field does not match the %s field.",
        "is_unique" => "The %s field must contain a unique value.",
        "is_natural" => "The %s field must contain only positive numbers.",
        "is_natural_no_zero" => "The %s field must contain a number greater than zero.",
        "decimal" => "The %s field must contain a decimal number.",
        "less_than" => "The %s field must contain a number less than %s.",
        "greater_than" => "The %s field must contain a number greater than %s."
    );

    function __construct() {
        throw new Exception("Don't instanciate the FormInput class - all methods are static.");
    }

    public static function getInput($validation=false, $dont_escape_sql = false, $convertUnicode = true) {
        if($validation !== false){
            if(self::performValidation($validation, $_REQUEST) !== true){
                // performValidation should've thrown an exception if validation fails, shouldn't get to here.
                return false;
            }
        }
        if($dont_escape_sql !== true){
            return self::cleanArray($_REQUEST, 'cleanInputKeepHtml', $convertUnicode);
        } else {
            return self::cleanArray($_REQUEST, 'cleanInputKeepHtmlKeepSql', $convertUnicode);
        }
    }


    public static function getPostInput($validation=false, $dont_escape_sql = false, $convertUnicode = true) {
        if($validation !== false){
            if(self::performValidation($validation, $_POST) !== true){
                // performValidation should've thrown an exception if validation fails, shouldn't get to here.
                return false;
            }
        }

        if($dont_escape_sql !== true){
            return self::cleanArray($_POST, 'cleanInputKeepHtml', $convertUnicode);
        } else {
            return self::cleanArray($_POST, 'cleanInputKeepHtmlKeepSql', $convertUnicode);
        }
    }

    public static function getGetInput($validation=false, $dont_escape_sql = false, $convertUnicode = true) {
        if($validation !== false){
            if(self::performValidation($validation, $_GET) !== true){
                // performValidation should've thrown an exception if validation fails, shouldn't get to here.
                return false;
            }
        }

        if($dont_escape_sql !== true){
            return self::cleanArray($_GET, 'cleanInputKeepHtml', $convertUnicode);
        } else {
            return self::cleanArray($_GET, 'cleanInputKeepHtmlKeepSql', $convertUnicode);
        }
    }

    public static function getPostInputKeepHtml($convertUnicode = true) {
        return self::cleanArray($_POST, 'cleanInputKeepHtml', $convertUnicode);
    }

    public static function cleanArray($inputArray, $cleanFunction = 'cleanInput', $convertUnicode = true) {
        $clean = array();
        if (is_array($inputArray)) {
            foreach($inputArray as $key => $value) {
                if (is_array($value)) {
                    $clean[$key] = self::cleanArray($value, $cleanFunction, $convertUnicode);
                } else {
                    $fn = array('self', $cleanFunction);
                    $clean[$key] = call_user_func($fn, $value, $convertUnicode);
                }
            }
            return $clean;
        }
        else {
            if ($inputArray !== NULL) {
                return self::cleanInput($inputArray, $convertUnicode);
            } return $inputArray;
        }
    }

    public static function cleanInput($inputValue, $convertUnicode = true) {
        if ($convertUnicode) {
            $inputValue = convert_from_unicode($inputValue);
        }
        $inputValue = self::cleanInputKeepHtml($inputValue, $convertUnicode);
        $inputValue = self::removeHtml($inputValue);
        return $inputValue;
    }

    public static function cleanInputKeepHtml($inputValue, $convertUnicode = true) {
        if ($convertUnicode) {
            $inputValue = convert_from_unicode($inputValue);
        }
        if(get_magic_quotes_gpc()) {
            $inputValue = stripslashes($inputValue);
        }

        return self::removeSql($inputValue);
    }
    public static function cleanInputKeepHtmlKeepSql($inputValue, $convertUnicode = true) {
        if ($convertUnicode) {
            $inputValue = convert_from_unicode($inputValue);
        }
        if(get_magic_quotes_gpc()) {
            $inputValue = stripslashes($inputValue);
        }
        return $inputValue;
    }

    public static function cleanInputKeepAllowTagsKeepSql($inputValue, $allowable_tags, $convertUnicode = true) {
        if ($convertUnicode) {
            $inputValue = convert_from_unicode($inputValue);
        }
        if(get_magic_quotes_gpc()) {
            $inputValue = stripslashes($inputValue);
        }
        $inputValue = self::removeHtmlKeepAllowTags($inputValue, $allowable_tags);
        return $inputValue;
    }

    public static function cleanInputKeepAllowTags($inputValue, $allowable_tags, $convertUnicode = true) {
        if ($convertUnicode) {
            $inputValue = convert_from_unicode($inputValue);
        }
        $inputValue = self::cleanInputKeepHtml($inputValue, $convertUnicode);
        $inputValue = self::removeHtmlKeepAllowTags($inputValue, $allowable_tags);
        return $inputValue;
    }


    public static function removeHtml($inputValue) {
        return htmlentities($inputValue, ENT_QUOTES, "UTF-8");
    }

    public static function removeHtmlKeepAllowTags($inputValue, $allowable_tags) {
        return strip_tags($inputValue, $allowable_tags);
    }

    public static function removeSql($inputValue) {
        global $db_link;

        if($db_link) {
            return mysql_real_escape_string($inputValue);
        } else {
            return addslashes($inputValue);
        }
    }

    public static function performValidation($validation_array, $data_input) {
        $validator = new FL_Form_validation($data_input);
        $validator->set_message(self::$error_msgs);
        $validator->set_rules($validation_array);

        if ($validator->run() !== true) {
            $validate_fail_message = $validator->error_string();
            $errorfields = $validator->error_array();
            $validatorException = new ValidationException($validate_fail_message);
            $validatorException->setErrors($errorfields);
            throw $validatorException;
        } else {
            return true;
        }
    }

}
