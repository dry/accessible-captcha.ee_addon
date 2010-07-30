<?php if ( ! defined('EXT')) exit('Invalid file request');

/**
 * An ExpressionEngine Extension that changes the default graphic
 * captcha into a question & answer based one.
 *
 * @package		Accessible Captcha
 * @author		Greg Salt <drylouvre> <greg@purple-dogfish.co.uk>
 * @copyright	Copyright (c) 2009 - 2010 Purple Dogfish Ltd
 * @license		http://www.purple-dogfish.co.uk/licence/free
 * @link		http://www.purple-dogfish.co.uk/free-stuff/accessible-captcha-2.x
 * @since		Version 2.1.
 * 
 */

/**
 * Changelog
 * =========
 * Version 1.0.4 20100725
 * ----------------------
 * Replaced 'pur' prefix with 'dry'
 * Implemented dynamic question and answer pairs
 * Fully MSM compatible i.e. use different Q&A pairs in sites
 *
 * Version 1.0.3
 * -------------
 * Fixed bug random settings array selection
 *
 * Version 1.0.2
 * -------------
 * Added additional setting to enable hint wrapped in brackets
 * Amended English hints language text
 *
 * Version 1.0.1
 * -------------
 * Added language key for answer hints
 * Added 3 new Q&A lines
 *
 * Version 1.0
 * -----------
 * Initial public release
 */
class Dry_accessible_captcha
{
    var $settings        = array();
    
    var $name            = 'Accessible Captcha';
    var $version         = '1.0.4';
    var $description     = 'Convert the default graphic captcha into an accessible (and more secure) version using questions and answers';
    var $settings_exist  = 'y';
    var $docs_url        = 'http://www.purple-dogfish.co.uk/free-stuff/accessible-captcha-1.x';
    
    // ------------------------------------------------
    //   Constructor - Extensions use this for settings
    // ------------------------------------------------
    
    function Dry_accessible_captcha($settings='')
    {
        $this->settings = $settings;
    }
    // END
    
    // ------------------------------------------------
    //   Create the new captcha
    // ------------------------------------------------
    
    function create_captcha($settings='')
    {
        global $DSP, $DB, $IN, $EXT, $LANG;
    	
    	$lw = '';
    	$rw = '';
    	
    	$LANG->fetch_language_file('accessible_captcha');
    	
    	$EXT->end_script = TRUE;

    	while(@$answer == ''):

      		$seed = rand(1,(count($this->settings)-2)/2);

    		$question = 'question'."$seed";
    		$question = $this->settings[$question];
    	
    		$answer = 'answer'."$seed";
 			$answer = $this->settings[$answer];

    	endwhile;

    	$DB->query("INSERT INTO exp_captcha (date, ip_address, word) VALUES (UNIX_TIMESTAMP(), '".$IN->IP."', '".$DB->escape_str($answer)."')");
    	
    	$this->cached_captcha = $answer;
    	
    	if($this->settings['hints_wrap'] == 'yes')
    	{
    		$lw = '(';
    		$rw = ')';
    	}
    	
		if($this->settings['hints'] == 'yes')
			$question .= ' <span class="captcha-hints">' . $lw . strlen($answer) . ' ' . $LANG->line('characters_required') . $rw . '</span>';
			
		return $question;
    	
    }
    // END
    
    // --------------------------------
	//  Settings
	// --------------------------------  

	function settings()
	{
    	$settings = array();
    
		$settings['hints'] = array('r', array('yes' => "yes", 'no' => "no"), 'no');
		$settings['hints_wrap'] = array('r', array('yes' => "yes", 'no' => "no"), 'no');
	    $settings['question1'] = '';
	    $settings['answer1'] = '';
	    $settings['question2'] = '';
	    $settings['answer2'] = '';
	    $settings['question3'] = '';
	    $settings['answer3'] = '';
		$settings['question4'] = '';
		$settings['answer4'] = '';
		$settings['question5'] = '';
		$settings['answer5'] = '';
		$settings['question6'] = '';
		$settings['answer6'] = '';
		$settings['question7'] = '';
		$settings['answer7'] = '';
		$settings['question8'] = '';
		$settings['answer8'] = '';
 
	    return $settings;
	}
	// END

    // --------------------------------
	//  Activate Extension
	// --------------------------------

	function activate_extension()
	{
	    global $DB;
    
	    $DB->query($DB->insert_string('exp_extensions',
                                  array(
                                  'extension_id' => '',
                                  'class'        => "Dry_accessible_captcha",
                                  'method'       => "create_captcha",
                                  'hook'         => "create_captcha_start",
                                  'settings'     => "",
                                  'priority'     => 10,
                                  'version'      => $this->version,
                                  'enabled'      => "y"
	                                				)
                                 	)
              	);
	}
	// END
	
	// --------------------------------
	//  Update Extension
	// --------------------------------  

	function update_extension($current='')
	{
	    global $DB;
    
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
    
	    $DB->query("UPDATE exp_extensions 
                SET version = '".$DB->escape_str($this->version)."' 
                WHERE class = 'Example_extension'");
	}
	// END
	
	// --------------------------------
	//  Disable Extension
	// --------------------------------

	function disable_extension()
	{
    	global $DB;
    
	    $DB->query("DELETE FROM exp_extensions WHERE class = 'Dry_accessible_captcha'");
	}
	// END

}
// END CLASS
?>