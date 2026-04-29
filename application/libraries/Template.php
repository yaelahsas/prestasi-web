<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template Library
 * 
 * Manages the layout templating system for consistent UI across all pages.
 * Renders header, sidebar, topbar, content, and footer partials.
 */
class Template {

    protected $CI;

    // Default configuration
    protected $layout = 'layouts/main';
    protected $active_menu = '';
    protected $page_title = 'Sistem Prestasi';
    protected $additional_css = '';
    protected $additional_js = '';
    protected $additional_js_files = array();
    protected $additional_css_files = array();
    protected $body_class = 'bg-gradient-to-br from-school-light-green to-white min-h-screen';

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Set the active menu item
     * @param string $menu
     * @return $this
     */
    public function set_active_menu($menu)
    {
        $this->active_menu = $menu;
        return $this;
    }

    /**
     * Set the page title
     * @param string $title
     * @return $this
     */
    public function set_title($title)
    {
        $this->page_title = $title;
        return $this;
    }

    /**
     * Set body class
     * @param string $class
     * @return $this
     */
    public function set_body_class($class)
    {
        $this->body_class = $class;
        return $this;
    }

    /**
     * Add inline CSS
     * @param string $css
     * @return $this
     */
    public function add_css($css)
    {
        $this->additional_css .= $css;
        return $this;
    }

    /**
     * Add inline JS
     * @param string $js
     * @return $this
     */
    public function add_js($js)
    {
        $this->additional_js .= $js;
        return $this;
    }

    /**
     * Add external JS file
     * @param string $file
     * @return $this
     */
    public function add_js_file($file)
    {
        $this->additional_js_files[] = $file;
        return $this;
    }

    /**
     * Add external CSS file
     * @param string $file
     * @return $this
     */
    public function add_css_file($file)
    {
        $this->additional_css_files[] = $file;
        return $this;
    }

    /**
     * Render the complete page with layout
     * @param string $content_view Path to the content view
     * @param array $data Data to pass to views
     * @return void
     */
    public function render($content_view, $data = array())
    {
        // Build template data
        $template_data = array(
            'page_title'        => $this->page_title,
            'active_menu'       => $this->active_menu,
            'body_class'        => $this->body_class,
            'additional_css'    => $this->additional_css,
            'additional_js'     => $this->additional_js,
            'additional_js_files' => $this->additional_js_files,
            'additional_css_files' => $this->additional_css_files,
            'content_view'      => $content_view,
            'user'              => $this->CI->session->userdata(),
        );

        // Merge with provided data (data takes precedence for content)
        $template_data = array_merge($template_data, $data);

        // Ensure user data is always from session
        if (isset($data['user'])) {
            $template_data['user'] = $data['user'];
        }

        // Load the main layout
        $this->CI->load->view($this->layout, $template_data);
    }
}
