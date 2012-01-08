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
 * preparetheme
 *
 * add some information to the theme header
 */
function MultiHook_themeapi_preparetheme()
{
    $dom = ZLanguage::getModuleDomain('MultiHook');

    if(SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        PageUtil::addVar('stylesheet', ThemeUtil::getModuleStyleSheet('MultiHook'));
        PageUtil::addVar('javascript', 'javascript/ajax/prototype.js');
        PageUtil::addVar('javascript', 'javascript/ajax/effects.js');
        PageUtil::addVar('javascript', 'javascript/ajax/dragdrop.js');
        PageUtil::addVar('javascript', 'modules/MultiHook/pnjavascript/multihook.js');
        PageUtil::addVar('rawtext', '<script type="text/javascript">var mhloadingText = "' . DataUtil::formatForDisplay(__('Loading data...', $dom)) .'"; var mhsavingText = "' . DataUtil::formatForDisplay(__('Saving data...', $dom)) . '";</script>');
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
    if(SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
        $dom = ZLanguage::getModuleDomain('MultiHook');
        $render = pnRender::getInstance('MultiHook', false);
        $render->assign('userlang', ZLanguage::getLanguageCode());
        $render->assign('modinfo', pnModGetInfo(pnModGetIDFromName('MultiHook')));
        $out = $render->fetch('mh_dynamic_hiddenform.html');
    }
    return $out;
}
