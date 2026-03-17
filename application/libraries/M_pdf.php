<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_pdf
{
    function __construct()
    {
        $CI = &get_instance();
        log_message('debug', 'mPDF class is loaded.');
    }

    function load($param = NULL)
    {
        include_once APPPATH . 'libraries/MPDF57/mpdf.php';

        if ($param == NULL) {
            return new mPDF('en-GB-x', 'A5', '', '', 10, 10, 10, 10, 6, 3);
        }

        return new mPDF($param);
    }

    function load_a4($param = NULL)
    {
        include_once APPPATH . 'libraries/MPDF57/mpdf.php';

        if ($param == NULL) {
            return new mPDF('en-GB-x', 'A4', '', '', 10, 10, 10, 10, 6, 3);
        }

        return new mPDF($param);
    }

    function load_c($param = NULL)
    {
        include_once APPPATH . 'libraries/MPDF57/mpdf.php';

        if ($param == NULL) {
            return new mPDF('en-GB-x', 'A5');
        }

        return new mPDF($param);
    }
}