<?php
// $Id$
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Frank Schummertz
// Purpose of file:  MultiHook theme functions
// ----------------------------------------------------------------------

/**
 * preparetheme
 *
 * add some information to the theme header
 */
function MultiHook_themeapi_preparetheme()
{
    $type = FormUtil::getPassedValue('type', '', 'GETPOST');
    if($type <> 'admin' && SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        pnPageAddVar('stylesheet', 'modules/MultiHook/pnstyle/style.css');
        pnPageAddVar('javascript', 'javascript/ajax/prototype.js');
        pnPageAddVar('javascript', 'javascript/ajax/effects.js');
        pnPageAddVar('javascript', 'javascript/ajax/dragdrop.js');
        pnPageAddVar('javascript', 'modules/MultiHook/pnjavascript/multihook.js');
    }
    return true;
}


/**
 * helper
 *
 * Implements hidden divs and javascript for Ajax usage. Used in the
 * multihookhelper plugin, can also be called from legacy themes or AutoThemes
 * if necessary.
 */
function MultiHook_themeapi_helper()
{
    $out = '';
    $type = FormUtil::getPassedValue('type', '', 'GETPOST');
    if($type <> 'admin' && SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        pnModLangLoad('MultiHook', 'admin');
        $pnr = new pnRender('MultiHook', false);
        $pnr->assign('userlang', pnUserGetLang());
        $pnr->assign('modinfo', pnModGetInfo(pnModGetIDFromName('MultiHook')));
        $out = $pnr->fetch('mh_dynamic_hiddenform.html');
    }
    return $out;
}


?>