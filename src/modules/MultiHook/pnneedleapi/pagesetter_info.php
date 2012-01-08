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
 * pagesetter needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_pagesetter_info()
{
    $info = array('module'  => 'pagesetter', 
                  'info'    => 'PAGESETTER{tid}{-pid}',
                  'inspect' => false);
    return $info;
}
