<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: photoshare_info.php 218 2009-11-16 12:29:18Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

/**
 * photoshare needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_photoshare_info()
{
    $info = array('module'  => 'photoshare', 
                  'info'    => 'PHOTOSHARE{A-albumid|P-pictureid|T-pictureid}',
                  'inspect' => false);
    return $info;
}
