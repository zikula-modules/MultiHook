<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: function.multihookhelper.php 218 2009-11-16 12:29:18Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

function smarty_function_multihookhelper($params, &$smarty)
{
    if(ModUtil::available('MultiHook')) {
        $modinfo = ModUtil::getInfo(ModUtil::getIdFromName('MultiHook'));
        if(version_compare($modinfo['version'], '5.0', '>=')==1) {
            ModUtil::apiFunc('MultiHook', 'theme', 'preparetheme');
            return ModUtil::apiFunc('MultiHook', 'theme', 'helper'); 
        }
    }
}
