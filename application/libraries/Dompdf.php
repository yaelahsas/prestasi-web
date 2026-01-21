<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf as DompdfLib;
use Dompdf\Options;

class Dompdf
{
    private $dompdf;
    private $options;
    
    public function __construct()
    {
        $this->options = new Options();
        $this->options->set('defaultFont', 'Helvetica');
        $this->options->set('isRemoteEnabled', true);
        $this->options->set('isHtml5ParserEnabled', true);
        
        $this->dompdf = new DompdfLib($this->options);
    }
    
    /**
     * Set paper size and orientation
     * @param string $size Paper size (A4, Letter, etc.)
     * @param string $orientation Portrait or Landscape
     */
    public function setPaper($size = 'A4', $orientation = 'portrait')
    {
        $this->dompdf->setPaper($size, $orientation);
    }
    
    /**
     * Load HTML content
     * @param string $html HTML content to load
     */
    public function loadHtml($html)
    {
        $this->dompdf->loadHtml($html);
    }
    
    /**
     * Render the PDF
     */
    public function render()
    {
        $this->dompdf->render();
    }
    
    /**
     * Stream the PDF to the browser
     * @param string $filename Filename for the PDF
     * @param array $options Options for streaming
     */
    public function stream($filename, $options = [])
    {
        $this->dompdf->stream($filename, $options);
    }
    
    /**
     * Output the PDF as a string
     * @return string PDF content
     */
    public function output()
    {
        return $this->dompdf->output();
    }
    
    /**
     * Get the Dompdf instance for advanced usage
     * @return DompdfLib
     */
    public function getDompdf()
    {
        return $this->dompdf;
    }
    
    /**
     * Set font for the document
     * @param string $font Font family
     * @param string $style Font style (normal, bold, italic)
     * @param float $size Font size
     */
    public function setFont($font, $style = 'normal', $size = 12)
    {
        // DomPDF handles fonts through CSS, so we'll need to apply this via HTML
        // This is a placeholder for compatibility with TCPDF interface
    }
    
    /**
     * Add a page (for compatibility with TCPDF interface)
     * DomPDF handles pagination automatically
     */
    public function AddPage()
    {
        // DomPDF handles pagination automatically
        // This is a placeholder for compatibility with TCPDF interface
    }
    
    /**
     * Set margins (for compatibility with TCPDF interface)
     * DomPDF handles margins through CSS
     * @param float $left Left margin
     * @param float $top Top margin
     * @param float $right Right margin
     * @param float $bottom Bottom margin
     */
    public function SetMargins($left, $top, $right = null, $bottom = null)
    {
        // DomPDF handles margins through CSS
        // This is a placeholder for compatibility with TCPDF interface
    }
    
    /**
     * Set auto page breaks (for compatibility with TCPDF interface)
     * DomPDF handles page breaks automatically
     * @param bool $auto Enable or disable auto page breaks
     * @param float $margin Bottom margin
     */
    public function SetAutoPageBreak($auto, $margin = 0)
    {
        // DomPDF handles page breaks automatically
        // This is a placeholder for compatibility with TCPDF interface
    }
    
    /**
     * Set document information (for compatibility with TCPDF interface)
     * @param string $title Document title
     * @param string $author Document author
     * @param string $subject Document subject
     * @param string $keywords Document keywords
     */
    public function SetCreator($creator)
    {
        // DomPDF doesn't have direct creator setting
        // This is a placeholder for compatibility with TCPDF interface
    }
    
    public function SetAuthor($author)
    {
        // DomPDF doesn't have direct author setting
        // This is a placeholder for compatibility with TCPDF interface
    }
    
    public function SetTitle($title)
    {
        // DomPDF doesn't have direct title setting
        // This is a placeholder for compatibility with TCPDF interface
    }
}