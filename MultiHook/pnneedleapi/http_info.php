<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

/**
 * changeset needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_http_info()
{
    $info = array('module'        => 'MultiHook', // module name
                  'info'          => 'http://www.example.com',   // possible needles  
                  'inspect'       => true,
                  'needle'        => array('http://', 'https://', 'ftp://', 'mailto://'),
                  'function'      => 'http',
                  'casesensitive' => false); 
    return $info;
}
