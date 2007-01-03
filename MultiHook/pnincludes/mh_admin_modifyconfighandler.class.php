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
// Purpose of file:  MultiHook administration display functions
// ----------------------------------------------------------------------

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
        // Security check
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(pnModURL('MultiHook', 'admin', 'main'));
        }  
        if ($args['commandName'] == 'submit') {
            if (!$pnRender->pnFormIsValid()) {
                return false;
            }
            $data = $pnRender->pnFormGetValues();
            pnModSetVar('MultiHook', 'abacfirst',         $data['abacfirst']);
            pnModSetVar('MultiHook', 'mhincodetags',      $data['mhincodetags']);
            pnModSetVar('MultiHook', 'mhlinktitle',       $data['mhlinktitle']);
            pnModSetVar('MultiHook', 'mhreplaceabbr',     $data['mhreplaceabbr']);
            pnModSetVar('MultiHook', 'mhshoweditlink',    $data['mhshoweditlink']);
            pnModSetVar('MultiHook', 'itemsperpage',      $data['itemsperpage']);
            pnModSetVar('MultiHook', 'externallinkclass', $data['externallinkclass']);

            LogUtil::registerStatus(_MH_UPDATEDCONFIG);
        }
        return true;
    }

}

?>