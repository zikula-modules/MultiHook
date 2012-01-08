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

class MultiHook_admin_modifyconfighandler
{
    var $id;

    function initialize(&$pnRender)
    {
        $this->id = (int)FormUtil::getPassedValue('id', 0, 'GETPOST');
        $pnRender->caching = false;
        $pnRender->add_core_data();
        return true;
    }


    function handleCommand(&$pnRender, &$args)
    {
        $dom = ZLanguage::getModuleDomain('MultiHook');
        // Security check
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(pnModURL('MultiHook', 'admin', 'main'));
        }  
        if ($args['commandName'] == 'submit') {
            if (!$pnRender->pnFormIsValid()) {
                return false;
            }
            $data = $pnRender->pnFormGetValues();
            pnModSetVar('MultiHook', 'abacfirst',          $data['abacfirst']);
            pnModSetVar('MultiHook', 'mhincodetags',       $data['mhincodetags']);
            pnModSetVar('MultiHook', 'mhlinktitle',        $data['mhlinktitle']);
            pnModSetVar('MultiHook', 'mhreplaceabbr',      $data['mhreplaceabbr']);
            pnModSetVar('MultiHook', 'mhshoweditlink',     $data['mhshoweditlink']);
            pnModSetVar('MultiHook', 'itemsperpage',       $data['itemsperpage']);
            pnModSetVar('MultiHook', 'externallinkclass',  $data['externallinkclass']);
            pnModSetVar('MultiHook', 'mhbrutalcensor',     $data['mhbrutalcensor']);
            pnModSetVar('MultiHook', 'mhrelaxedcensoring', $data['mhrelaxedcensoring']);

            LogUtil::registerStatus(__('Done! Saved your settings changes.', $dom));
        }
        return true;
    }

}
