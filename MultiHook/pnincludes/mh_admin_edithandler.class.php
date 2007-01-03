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

class MultiHook_admin_edithandler
{
    var $aid;

    function initialize(&$pnRender)
    {
        $this->aid = (int)FormUtil::getPassedValue('aid', -1, 'GETPOST');
        $pnRender->caching = false;
        $pnRender->add_core_data();
            
        if(($this->aid==-1) ) {
            $abac = array( 'aid'   => -1,
                           'short' => '',
                           'long'  => '',
                           'title' => '',
                           'type'  => 0,
                           'language' => pnUserGetLang() );
        } else {
            $abac = pnModAPIFunc('MultiHook',
                                 'user',
                                 'get',
                                 array('aid' => $this->aid));
        
            if ($abac == false) {
                return LogUtil::registerError(_MH_NOSUCHITEM, pnModURL('MultiHook', 'admin', 'main'));
            }
            // set permission flags
            $abac['edit'] = false;
            $abac['delete'] = false;
        
            if (SecurityUtil::checkPermission('MultiHook::', "$abac[short]::$abac[aid]", ACCESS_EDIT)) {
                $abac['edit'] = true;
                if (SecurityUtil::checkPermission('MultiHook::', "$abac[short]::$abac[aid]", ACCESS_DELETE)) {
                    $abac['delete'] = true;
                }
            } else {
                return LogUtil::registerPermissionError(pnModURL('MultiHook','admin','main'));
            }
        
        }
        
        $pnRender->assign('abac', $abac);
        $pnRender->assign('types', array( _MH_TYPEABBREVIATION,
                                          _MH_TYPEACRONYM,
                                          _MH_TYPELINK,
                                          _MH_TYPEILLEGALWORD));

        return true;
    }


    function handleCommand(&$pnRender, &$args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(pnModURL('MultiHook', 'admin', 'main'));
        }  
        if ($args['commandName'] == 'submit') {
            $ok = $pnRender->pnFormIsValid();

            $data = $pnRender->pnFormGetValues();
            $data['aid'] = $this->aid;

            if(isset($data['mh_delete']) && ($data['mh_delete']==true) ) {
                // The API function is called
                if (pnModAPIFunc('MultiHook',
                                 'admin',
                                 'delete',
                                 array('aid' => $data['aid']))) {
                    // Success
                    LogUtil::registerStatus(_MH_DELETED);
                } else {
                    LogUtil::registerError(_MH_DELETEFAILED);
                }
                return pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter' => $data['type'])));
            }

            // no deletion, further checks needed
            if(empty($data['short'])) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_short');
                $ifield->setError(DataUtil::formatForDisplay(_MH_SHORTEMPTY));
                $ok = false;
            }
            if(empty($data['long']) && ($data['type']<>3)) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_long');
                $ifield->setError(DataUtil::formatForDisplay(_MH_LONGEMPTY));
                $ok = false;
            }
            if(($data['type']<0) || ($data['type']>3)) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_type');
                $ifield->setError(DataUtil::formatForDisplay(_MH_TYPEEMPTY . "($type)"));
                $ok = false;
            }
            if($data['type']==2 && empty($data['title'])) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_title');
                $ifield->setError(DataUtil::formatForDisplay(_MH_TITLEEMPTY));
                $ok = false;
            }
            
            if(!$ok) {
                return false;
            }

            if(empty($data['language'])) {
                $data['language'] = 'All';
            }

            // The API function is called
            if($data['aid'] == -1) {
                if(pnModAPIFunc('MultiHook',
                                    'admin',
                                    'create',
                                    $data) <> false) {
                    // Success
                    LogUtil::registerStatus( _MH_CREATED);
                } else {
                    LogUtil::registerError(_MH_CREATEDFAILED);
                }
            } else {
                if(pnModAPIFunc('MultiHook',
                                'admin',
                                'update',
                                $data) <> false) {
                    // Success
                    LogUtil::registerStatus(_MH_UPDATED);
                } else {
                    LogUtil::registerError(_MH_UPDATEFAILED);
                }
            }

        }
        return pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter' => $data['type'])));
    }

}

?>