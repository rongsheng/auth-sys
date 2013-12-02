<?php
/* Codeigniter class modified by Luke Anderson - February 2013
 *
 * Modifications have been marked with: // MODIFICATION
 *
 * Comments have been left in so that future updates of the codeigniter class can be easily integrated.
 */

class FL_Form_validation {

//    protected $CI; // MODIFICATION
    protected $_field_data            = array();
    protected $_config_rules        = array();
    protected $_error_array            = array();
    protected $_error_messages        = array();
    protected $_error_prefix        = '<p>';
    protected $_error_suffix        = '</p>';
    protected $error_string            = '';
    protected $_safe_form_data        = FALSE;
    protected $_input_data            = array();

    /**
     * Constructor
     */
    // MODIFICATION - $this->_input_data has been replaced everywhere with $this->_input_data, which is set in this constructor.
    public function __construct($data_input, $rules = array())
    {
    /* // MODIFICATION
        $this->CI =& get_instance();

        // Validation rules can be stored in a config file.
        $this->_config_rules = $rules;

        // Automatically load the form helper
        $this->CI->load->helper('form');

        // Set the character encoding in MB.
    */
        if (function_exists('mb_internal_encoding'))
        {
            // MODIFICATION
            mb_internal_encoding('UTF-8');
        }
    /* // MODIFICATION
        log_message('debug', "Form Validation Class Initialized");
    */
    // MODIFICATION - make work with POST or GET, instead of just post
    $this->_input_data = $data_input;

    }

    // --------------------------------------------------------------------

    /**
     * Set Rules
     *
     * This function takes an array of field names and validation
     * rules as input, validates the info, and stores it
     *
     * @access    public
     * @param    mixed
     * @param    string
     * @return    void
     */
    public function set_rules($field, $label = '', $rules = '')
    {
        // No reason to set rules if we have no POST data
        // MODIFICATION - We do want to set the rules if there's no data, in case there's a 'required' rule in there, so I've commented this block.
        /*
        if (count($this->_input_data) == 0)
        {
            return $this;
        }*/

        // If an array was passed via the first parameter instead of indidual string
        // values we cycle through it and recursively call this function.
        if (is_array($field))
        {
            foreach ($field as $row)
            {
                // Houston, we have a problem...
                if ( ! isset($row['field']) OR ! isset($row['rules']))
                {
                    continue;
                }

                // If the field label wasn't passed we use the field name
                $label = ( ! isset($row['label'])) ? $row['field'] : $row['label'];

                // Here we go!
                $this->set_rules($row['field'], $label, $row['rules']);
            }
            return $this;
        }

        // No fields? Nothing to do...
        if ( ! is_string($field) OR  ! is_string($rules) OR $field == '')
        {
            return $this;
        }

        // If the field label wasn't passed we use the field name
        $label = ($label == '') ? $field : $label;

        // Is the field name an array?  We test for the existence of a bracket "[" in
        // the field name to determine this.  If it is an array, we break it apart
        // into its components so that we can fetch the corresponding POST data later
        if (strpos($field, '[') !== FALSE AND preg_match_all('/\[(.*?)\]/', $field, $matches))
        {
            // Note: Due to a bug in current() that affects some versions
            // of PHP we can not pass function call directly into it
            $x = explode('[', $field);
            $indexes[] = current($x);

            for ($i = 0; $i < count($matches['0']); $i++)
            {
                if ($matches['1'][$i] != '')
                {
                    $indexes[] = $matches['1'][$i];
                }
            }

            $is_array = TRUE;
        }
        else
        {
            $indexes    = array();
            $is_array    = FALSE;
        }

        // Build our master array
        $this->_field_data[$field] = array(
            'field'                => $field,
            'label'                => $label,
            'rules'                => $rules,
            'is_array'            => $is_array,
            'keys'                => $indexes,
            'postdata'            => NULL,
            'error'                => ''
        );

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Error Message
     *
     * Lets users set their own error messages on the fly.  Note:  The key
     * name has to match the  function name that it corresponds to.
     *
     * @access    public
     * @param    string
     * @param    string
     * @return    string
     */
    public function set_message($lang, $val = '')
    {
        if ( ! is_array($lang))
        {
            $lang = array($lang => $val);
        }

        $this->_error_messages = array_merge($this->_error_messages, $lang);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set The Error Delimiter
     *
     * Permits a prefix/suffix to be added to each error message
     *
     * @access    public
     * @param    string
     * @param    string
     * @return    void
     */
    public function set_error_delimiters($prefix = '<p>', $suffix = '</p>')
    {
        $this->_error_prefix = $prefix;
        $this->_error_suffix = $suffix;

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Get Error Message
     *
     * Gets the error message associated with a particular field
     *
     * @access    public
     * @param    string    the field name
     * @return    void
     */
    public function error($field = '', $prefix = '', $suffix = '')
    {
        if ( ! isset($this->_field_data[$field]['error']) OR $this->_field_data[$field]['error'] == '')
        {
            return '';
        }

        if ($prefix == '')
        {
            $prefix = $this->_error_prefix;
        }

        if ($suffix == '')
        {
            $suffix = $this->_error_suffix;
        }

        return $prefix.$this->_field_data[$field]['error'].$suffix;
    }

        // --------------------------------------------------------------------

    /**
     * Get Array of Error Messages
     *
     * Returns the error messages as an array
     *
     * @return  array
     */
    public function error_array()
    {
        return $this->_error_array;
    }

    // --------------------------------------------------------------------

    // --------------------------------------------------------------------

    /**
     * Error String
     *
     * Returns the error messages as a string, wrapped in the error delimiters
     *
     * @access    public
     * @param    string
     * @param    string
     * @return    str
     */
    public function error_string($prefix = '', $suffix = '')
    {
        // No errrors, validation passes!
        if (count($this->_error_array) === 0)
        {
            return '';
        }

        if ($prefix == '')
        {
            $prefix = $this->_error_prefix;
        }

        if ($suffix == '')
        {
            $suffix = $this->_error_suffix;
        }

        // Generate the error string
        $str = '';

        foreach ($this->_error_array as $val)
        {
            if ($val != '')
            {
                $str .= $prefix.$val.$suffix."\n";
            }
        }

        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * Run the Validator
     *
     * This function does all the work.
     *
     * @access    public
     * @return    bool
     */
    public function run($group = '')
    {
        // Do we even have any data to process?  Mm?
        if (count($this->_input_data) == 0)
        {
            // MODIFICATION - Make it so that if there is no required rules, and no field is passed in, validation doesn't fail.
            foreach ($this->_field_data as $field => $row){
                if(in_array('required', explode('|', $row['rules']))){
                    return FALSE;
                }
            }
            return TRUE;
        }

        // Does the _field_data array containing the validation rules exist?
        // If not, we look to see if they were assigned via a config file
        if (count($this->_field_data) == 0)
        {
            // MODIFICATION - removed whole if statement contents, relates to _config_rules which is not used.
            throw new Exception('Validation rules not defined.');
        }

        // Load the language file containing error messages
        // MODIFICATION - commented below line
        // $this->CI->lang->load('form_validation');

        // Cycle through the rules for each field, match the
        // corresponding $this->_input_data item and test for errors
        foreach ($this->_field_data as $field => $row)
        {
            // Fetch the data from the corresponding $this->_input_data array and cache it in the _field_data array.
            // Depending on whether the field name is an array or a string will determine where we get it from.

            if ($row['is_array'] == TRUE)
            {
                $this->_field_data[$field]['postdata'] = $this->_reduce_array($this->_input_data, $row['keys']);
            }
            else
            {
                if (isset($this->_input_data[$field]) AND $this->_input_data[$field] != "")
                {
                    $this->_field_data[$field]['postdata'] = $this->_input_data[$field];
                }
            }

            $this->_execute($row, explode('|', $row['rules']), $this->_field_data[$field]['postdata']);
        }

        // Did we end up with any errors?
        $total_errors = count($this->_error_array);

        if ($total_errors > 0)
        {
            $this->_safe_form_data = TRUE;
        }

        // Now we need to re-set the POST data with the new, processed data
        $this->_reset_post_array();

        // No errors, validation passes!
        if ($total_errors == 0)
        {
            return TRUE;
        }

        // Validation fails
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * Traverse a multidimensional $this->_input_data array index until the data is found
     *
     * @access    private
     * @param    array
     * @param    array
     * @param    integer
     * @return    mixed
     */
    protected function _reduce_array($array, $keys, $i = 0)
    {
        if (is_array($array))
        {
            if (isset($keys[$i]))
            {
                if (isset($array[$keys[$i]]))
                {
                    $array = $this->_reduce_array($array[$keys[$i]], $keys, ($i+1));
                }
                else
                {
                    return NULL;
                }
            }
            else
            {
                return $array;
            }
        }

        return $array;
    }

    // --------------------------------------------------------------------

    /**
     * Re-populate the _POST array with our finalized and processed data
     *
     * @access    private
     * @return    null
     */
    protected function _reset_post_array()
    {
        foreach ($this->_field_data as $field => $row)
        {
            if ( ! is_null($row['postdata']))
            {
                if ($row['is_array'] == FALSE)
                {
                    if (isset($this->_input_data[$row['field']]))
                    {
                        $this->_input_data[$row['field']] = $this->prep_for_form($row['postdata']);
                    }
                }
                else
                {
                    // start with a reference
                    $post_ref =& $this->_input_data;

                    // before we assign values, make a reference to the right POST key
                    if (count($row['keys']) == 1)
                    {
                        $post_ref =& $post_ref[current($row['keys'])];
                    }
                    else
                    {
                        foreach ($row['keys'] as $val)
                        {
                            $post_ref =& $post_ref[$val];
                        }
                    }

                    if (is_array($row['postdata']))
                    {
                        $array = array();
                        foreach ($row['postdata'] as $k => $v)
                        {
                            $array[$k] = $this->prep_for_form($v);
                        }

                        $post_ref = $array;
                    }
                    else
                    {
                        $post_ref = $this->prep_for_form($row['postdata']);
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Executes the Validation routines
     *
     * @access    private
     * @param    array
     * @param    array
     * @param    mixed
     * @param    integer
     * @return    mixed
     */
    protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
    {
        // If the $this->_input_data data is an array we will run a recursive call
        if (is_array($postdata))
        {
            foreach ($postdata as $key => $val)
            {
                $this->_execute($row, $rules, $val, $cycles);
                $cycles++;
            }

            return;
        }

        // --------------------------------------------------------------------

        // If the field is blank, but NOT required, no further tests are necessary
        $callback = FALSE;
        if ( ! in_array('required', $rules) AND is_null($postdata))
        {
            // Before we bail out, does the rule contain a callback?
            if (preg_match("/(callback_\w+(\[.*?\])?)/", implode(' ', $rules), $match))
            {
                $callback = TRUE;
                $rules = (array('1' => $match[1]));
            }
            else
            {
                return;
            }
        }

        // --------------------------------------------------------------------

        // Isset Test. Typically this rule will only apply to checkboxes.
        if (is_null($postdata) AND $callback == FALSE)
        {
            if (in_array('isset', $rules, TRUE) OR in_array('required', $rules))
            {
                // Set the message type
                $type = (in_array('required', $rules)) ? 'required' : 'isset';

                if ( ! isset($this->_error_messages[$type]))
                {
                    // MODIFICATION - get rid of language if statement
                    $line = 'The field was not set';
                }
                else
                {
                    $line = $this->_error_messages[$type];
                }

                // Build the error message
                $message = sprintf($line, $this->_translate_fieldname($row['label']));

                // Save the error message
                $this->_field_data[$row['field']]['error'] = $message;

                if ( ! isset($this->_error_array[$row['field']]))
                {
                    $this->_error_array[$row['field']] = $message;
                }
            }

            return;
        }

        // --------------------------------------------------------------------

        // Cycle through each rule and run it
        foreach ($rules As $rule)
        {
            $_in_array = FALSE;

            // We set the $postdata variable with the current data in our master array so that
            // each cycle of the loop is dealing with the processed data from the last cycle
            if ($row['is_array'] == TRUE AND is_array($this->_field_data[$row['field']]['postdata']))
            {
                // We shouldn't need this safety, but just in case there isn't an array index
                // associated with this cycle we'll bail out
                if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles]))
                {
                    continue;
                }

                $postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
                $_in_array = TRUE;
            }
            else
            {
                $postdata = $this->_field_data[$row['field']]['postdata'];
            }

            // --------------------------------------------------------------------

            // Is the rule a callback?
            $callback = FALSE;
            if (substr($rule, 0, 9) == 'callback_')
            {
                $rule = substr($rule, 9);
                $callback = TRUE;
            }

            // Strip the parameter (if exists) from the rule
            // Rules can contain a parameter: max_length[5]
            $param = FALSE;
            if (preg_match("/(.*?)\[(.*)\]/", $rule, $match))
            {
                $rule    = $match[1];
                $param    = $match[2];
            }

            // Call the function that corresponds to the rule
            // MODIFICATION - remove contents of if statement, callbacks aren't supported.
            if ($callback === TRUE)
            {
                throw new Exception("Callback functions aren't supported.");
            }
            else
            {
                if ( ! method_exists($this, $rule))
                {
                    // If our own wrapper function doesn't exist we see if a native PHP function does.
                    // Users can use any native PHP function call that has one param.
                    if (function_exists($rule))
                    {
                        $result = $rule($postdata);

                        if ($_in_array == TRUE)
                        {
                            $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                        }
                        else
                        {
                            $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                        }
                    }
                    else
                    {
                        throw new Exception("Unable to find validation rule: ".$rule);
                    }

                    continue;
                }

                $result = $this->$rule($postdata, $param);

                if ($_in_array == TRUE)
                {
                    $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                }
                else
                {
                    $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                }
            }

            // Did the rule test negatively?  If so, grab the error.
            if ($result === FALSE)
            {
                if ( ! isset($this->_error_messages[$rule]))
                {
                    // MODIFICATION - remove if statement, not doing translations.
                    $line = 'Unable to access an error message corresponding to your field name.';
                }
                else
                {
                    $line = $this->_error_messages[$rule];
                }

                // Is the parameter we are inserting into the error message the name
                // of another field?  If so we need to grab its "field label"
                if (isset($this->_field_data[$param]) AND isset($this->_field_data[$param]['label']))
                {
                    $param = $this->_translate_fieldname($this->_field_data[$param]['label']);
                }

                // Build the error message
                $message = sprintf($line, $this->_translate_fieldname($row['label']), $param);

                // Save the error message
                $this->_field_data[$row['field']]['error'] = $message;

                if ( ! isset($this->_error_array[$row['field']]))
                {
                    $this->_error_array[$row['field']] = $message;
                }

                return;
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Translate a field name
     *
     * @access    private
     * @param    string    the field name
     * @return    string
     */
    // MODIFICATION - get rid of all of this function, just return the input.
    protected function _translate_fieldname($fieldname)
    {
        return $fieldname;
    }

    // --------------------------------------------------------------------

    /**
     * Get the value from a form
     *
     * Permits you to repopulate a form field with the value it was submitted
     * with, or, if that value doesn't exist, with the default
     *
     * @access    public
     * @param    string    the field name
     * @param    string
     * @return    void
     */
    public function set_value($field = '', $default = '')
    {
        if ( ! isset($this->_field_data[$field]))
        {
            return $default;
        }

        // If the data is an array output them one at a time.
        //     E.g: form_input('name[]', set_value('name[]');
        if (is_array($this->_field_data[$field]['postdata']))
        {
            return array_shift($this->_field_data[$field]['postdata']);
        }

        return $this->_field_data[$field]['postdata'];
    }

    // --------------------------------------------------------------------

    /**
     * Set Select
     *
     * Enables pull-down lists to be set to the value the user
     * selected in the event of an error
     *
     * @access    public
     * @param    string
     * @param    string
     * @return    string
     */
    public function set_select($field = '', $value = '', $default = FALSE)
    {
        if ( ! isset($this->_field_data[$field]) OR ! isset($this->_field_data[$field]['postdata']))
        {
            if ($default === TRUE AND count($this->_field_data) === 0)
            {
                return ' selected="selected"';
            }
            return '';
        }

        $field = $this->_field_data[$field]['postdata'];

        if (is_array($field))
        {
            if ( ! in_array($value, $field))
            {
                return '';
            }
        }
        else
        {
            if (($field == '' OR $value == '') OR ($field != $value))
            {
                return '';
            }
        }

        return ' selected="selected"';
    }

    // --------------------------------------------------------------------

    /**
     * Set Radio
     *
     * Enables radio buttons to be set to the value the user
     * selected in the event of an error
     *
     * @access    public
     * @param    string
     * @param    string
     * @return    string
     */
    public function set_radio($field = '', $value = '', $default = FALSE)
    {
        if ( ! isset($this->_field_data[$field]) OR ! isset($this->_field_data[$field]['postdata']))
        {
            if ($default === TRUE AND count($this->_field_data) === 0)
            {
                return ' checked="checked"';
            }
            return '';
        }

        $field = $this->_field_data[$field]['postdata'];

        if (is_array($field))
        {
            if ( ! in_array($value, $field))
            {
                return '';
            }
        }
        else
        {
            if (($field == '' OR $value == '') OR ($field != $value))
            {
                return '';
            }
        }

        return ' checked="checked"';
    }

    // --------------------------------------------------------------------

    /**
     * Set Checkbox
     *
     * Enables checkboxes to be set to the value the user
     * selected in the event of an error
     *
     * @access    public
     * @param    string
     * @param    string
     * @return    string
     */
    public function set_checkbox($field = '', $value = '', $default = FALSE)
    {
        if ( ! isset($this->_field_data[$field]) OR ! isset($this->_field_data[$field]['postdata']))
        {
            if ($default === TRUE AND count($this->_field_data) === 0)
            {
                return ' checked="checked"';
            }
            return '';
        }

        $field = $this->_field_data[$field]['postdata'];

        if (is_array($field))
        {
            if ( ! in_array($value, $field))
            {
                return '';
            }
        }
        else
        {
            if (($field == '' OR $value == '') OR ($field != $value))
            {
                return '';
            }
        }

        return ' checked="checked"';
    }

    // --------------------------------------------------------------------

    /**
     * Required
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function required($str)
    {
        if ( ! is_array($str))
        {
            return (trim($str) == '') ? FALSE : TRUE;
        }
        else
        {
            return ( ! empty($str));
        }
    }

    // --------------------------------------------------------------------

    /**
     * Performs a Regular Expression match test.
     *
     * @access    public
     * @param    string
     * @param    regex
     * @return    bool
     */
    public function regex_match($str, $regex)
    {
        if ( ! preg_match($regex, $str))
        {
            return FALSE;
        }

        return  TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Match one field to another
     *
     * @access    public
     * @param    string
     * @param    field
     * @return    bool
     */
    public function matches($str, $field)
    {
        if ( ! isset($this->_input_data[$field]))
        {
            return FALSE;
        }

        $field = $this->_input_data[$field];

        return ($str !== $field) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Match one field to another
     *
     * @access    public
     * @param    string
     * @param    field
     * @return    bool
     */
    // MODIFICATION - is_unique is not supported, just throw an exception.
    public function is_unique($str, $field)
    {
        throw new Exception("is_unique validation function is not supported.");
    }

    // --------------------------------------------------------------------

    /**
     * Minimum Length
     *
     * @access    public
     * @param    string
     * @param    value
     * @return    bool
     */
    public function min_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return FALSE;
        }

        if (function_exists('mb_strlen'))
        {
            return (mb_strlen($str) < $val) ? FALSE : TRUE;
        }

        return (strlen($str) < $val) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Max Length
     *
     * @access    public
     * @param    string
     * @param    value
     * @return    bool
     */
    public function max_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return FALSE;
        }

        if (function_exists('mb_strlen'))
        {
            return (mb_strlen($str) > $val) ? FALSE : TRUE;
        }

        return (strlen($str) > $val) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Exact Length
     *
     * @access    public
     * @param    string
     * @param    value
     * @return    bool
     */
    public function exact_length($str, $val)
    {
        if (preg_match("/[^0-9]/", $val))
        {
            return FALSE;
        }

        if (function_exists('mb_strlen'))
        {
            return (mb_strlen($str) != $val) ? FALSE : TRUE;
        }

        return (strlen($str) != $val) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Valid Email
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function valid_email($str)
    {
        return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Valid Emails
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function valid_emails($str)
    {
        if (strpos($str, ',') === FALSE)
        {
            return $this->valid_email(trim($str));
        }

        foreach (explode(',', $str) as $email)
        {
            if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE)
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Validate IP Address
     *
     * @access    public
     * @param    string
     * @param    string "ipv4" or "ipv6" to validate a specific ip format
     * @return    string
     */
    // MODIFICATION - valid_ip has been cut from the core/Input.php part of codeigniter.
    public function valid_ip($ip, $which = '')
    {
        $which = strtolower($which);

        if ($which !== 'ipv6' && $which !== 'ipv4')
        {
            if (strpos($ip, ':') !== FALSE)
            {
                $which = 'ipv6';
            }
            elseif (strpos($ip, '.') !== FALSE)
            {
                $which = 'ipv4';
            }
            else
            {
                return FALSE;
            }
        }

        $func = '_valid_'.$which;
        return $this->$func($ip);
    }

    protected function _valid_ipv4($ip)
    {
        $ip_segments = explode('.', $ip);

        // Always 4 segments needed
        if (count($ip_segments) !== 4)
        {
            return FALSE;
        }
        // IP can not start with 0
        if ($ip_segments[0][0] == '0')
        {
            return FALSE;
        }

        // Check each segment
        foreach ($ip_segments as $segment)
        {
            // IP segments must be digits and can not be
            // longer than 3 digits or greater then 255
            if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
            {
                return FALSE;
            }
        }

        return TRUE;
    }
    protected function _valid_ipv6($str)
    {
        // 8 groups, separated by :
        // 0-ffff per group
        // one set of consecutive 0 groups can be collapsed to ::

        $groups = 8;
        $collapsed = FALSE;

        $chunks = array_filter(
            preg_split('/(:{1,2})/', $str, NULL, PREG_SPLIT_DELIM_CAPTURE)
        );

        // Rule out easy nonsense
        if (current($chunks) == ':' OR end($chunks) == ':')
        {
            return FALSE;
        }

        // PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
        if (strpos(end($chunks), '.') !== FALSE)
        {
            $ipv4 = array_pop($chunks);

            if ( ! $this->_valid_ipv4($ipv4))
            {
                return FALSE;
            }

            $groups--;
        }

        while ($seg = array_pop($chunks))
        {
            if ($seg[0] == ':')
            {
                if (--$groups == 0)
                {
                    return FALSE;    // too many groups
                }

                if (strlen($seg) > 2)
                {
                    return FALSE;    // long separator
                }

                if ($seg == '::')
                {
                    if ($collapsed)
                    {
                        return FALSE;    // multiple collapsed
                    }

                    $collapsed = TRUE;
                }
            }
            elseif (preg_match("/[^0-9a-f]/i", $seg) OR strlen($seg) > 4)
            {
                return FALSE; // invalid segment
            }
        }

        return $collapsed OR $groups == 1;
    }

    // --------------------------------------------------------------------

    /**
     * Alpha
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function alpha($str)
    {
        return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function alpha_numeric($str)
    {
        return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric with underscores and dashes
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function alpha_dash($str)
    {
        return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Numeric
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function numeric($str)
    {
        return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

    }

    // --------------------------------------------------------------------

    /**
     * Is Numeric
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function is_numeric($str)
    {
        return ( ! is_numeric($str)) ? FALSE : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Integer
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function integer($str)
    {
        return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Decimal number
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function decimal($str)
    {
        return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Greather than
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function greater_than($str, $min)
    {
        if ( ! is_numeric($str))
        {
            return FALSE;
        }
        return $str > $min;
    }

    // --------------------------------------------------------------------

    /**
     * Less than
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function less_than($str, $max)
    {
        if ( ! is_numeric($str))
        {
            return FALSE;
        }
        return $str < $max;
    }

    // --------------------------------------------------------------------

    /**
     * Is a Natural number  (0,1,2,3, etc.)
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function is_natural($str)
    {
        return (bool) preg_match( '/^[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Is a Natural number, but not a zero  (1,2,3, etc.)
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function is_natural_no_zero($str)
    {
        if ( ! preg_match( '/^[0-9]+$/', $str))
        {
            return FALSE;
        }

        if ($str == 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Valid Base64
     *
     * Tests a string for characters outside of the Base64 alphabet
     * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
     *
     * @access    public
     * @param    string
     * @return    bool
     */
    public function valid_base64($str)
    {
        return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Prep data for form
     *
     * This function allows HTML to be safely shown in a form.
     * Special characters are converted.
     *
     * @access    public
     * @param    string
     * @return    string
     */
    public function prep_for_form($data = '')
    {
        if (is_array($data))
        {
            foreach ($data as $key => $val)
            {
                $data[$key] = $this->prep_for_form($val);
            }

            return $data;
        }

        if ($this->_safe_form_data == FALSE OR $data === '')
        {
            return $data;
        }

        return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
    }

    // --------------------------------------------------------------------

    /**
     * Prep URL
     *
     * @access    public
     * @param    string
     * @return    string
     */
    public function prep_url($str = '')
    {
        if ($str == 'http://' OR $str == '')
        {
            return '';
        }

        if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
        {
            $str = 'http://'.$str;
        }

        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * Strip Image Tags
     *
     * @access    public
     * @param    string
     * @return    string
     */
    // MODIFICATION - this isn't supported, throw exception
    public function strip_image_tags($str)
    {
        throw new Exception("strip_image_tags validation function is not supported.");
    }

    // --------------------------------------------------------------------

    /**
     * XSS Clean
     *
     * @access    public
     * @param    string
     * @return    string
     */
    // MODIFICATION - this isn't supported, throw exception
    public function xss_clean($str)
    {
        throw new Exception("xss_clean validation function is not supported.");
    }

    // --------------------------------------------------------------------

    /**
     * Convert PHP tags to entities
     *
     * @access    public
     * @param    string
     * @return    string
     */
    public function encode_php_tags($str)
    {
        return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
    }

}
// END Form Validation Class

/* End of file Form_validation.php */
/* Location: ./system/libraries/Form_validation.php */
