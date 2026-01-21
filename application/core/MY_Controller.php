<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Ensure core classes are loaded as properties
        $this->load->library('session');
        $this->load->library('form_validation');
        
        // Make sure input is available
        if (!isset($this->input)) {
            $this->input =& load_class('Input', 'core');
        }
    }
}