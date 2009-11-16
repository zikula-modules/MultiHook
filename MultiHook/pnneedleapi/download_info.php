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
 * download needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_download_info()
{
    $info = array('module'  => 'Downloads', 
                  'info'    => 'DOWNLOAD{C-downloadcategoryid|D-downloadid|L-downloadid|S}',
                  'inspect' => false);
    return $info;
}
