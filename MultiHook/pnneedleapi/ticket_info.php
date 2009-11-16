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
 * ticket needle info
 * @param none
 * @return string with short usage description
 */
function MultiHook_needleapi_ticket_info()
{
    $info = array('module'  => 'MultiHook', // module name
                  'info'    => 'TICKET-{projectname-ticketid}',   // possible needles  
                  'inspect' => true);     //reverse lookpup possible, needs MultiHook_needleapi_pnforum_inspect() function
    return $info;
}
