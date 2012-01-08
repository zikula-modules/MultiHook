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
 * weblink needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_weblink_info()
{
    $info = array('module'  => 'Web_Links', 
                  'info'    => 'WEBLINK{C-weblinkcategoryid|D-weblinkid|L-weblinkid|S}',
                  'inspect' => false);
    return $info;
}
