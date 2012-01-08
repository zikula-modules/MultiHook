<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: changeset_info.php 218 2009-11-16 12:29:18Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

/**
 * changeset needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_changeset_info()
{
    $info = array('module'  => 'MultiHook', // module name
                  'info'    => 'CHANGESET-{projectname-changesetid}',   // possible needles  
                  'inspect' => true);     //reverse lookpup possible, needs MultiHook_needleapi_pnforum_inspect() function
    return $info;
}
