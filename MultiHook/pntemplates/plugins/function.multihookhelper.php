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

function smarty_function_multihookhelper($params, &$smarty)
{
    if(pnModAvailable('MultiHook')) {
        $modinfo = pnModGetInfo(pnModGetIDFromName('MultiHook'));
        if(version_compare($modinfo['version'], '5.0', '>=')==1) {
            pnModAPIFunc('MultiHook', 'theme', 'preparetheme');
            return pnModAPIFunc('MultiHook', 'theme', 'helper'); 
        }
    }
}
