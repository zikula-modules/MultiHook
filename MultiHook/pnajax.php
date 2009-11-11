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
// Purpose of file:  Ajax functions
// ----------------------------------------------------------------------

Loader::includeOnce('modules/MultiHook/common.php');

function MultiHook_ajax_read()
{
    $error = '';
    $return = '';

    $aid = (int)FormUtil::getPassedValue('mh_aid', -1, 'POST');
    $abac = pnModAPIFunc('MultiHook', 'user', 'get',
                         array('aid' => $aid));
    if(is_array($abac)) {
        AjaxUtil::output($abac);
        exit;
    } else {
        AjaxUtil::error(_MH_ERRORREADINGDATA . ' (aid=' . $aid . ')', '404 Not found');
    }
    // we should never get here
}

function MultiHook_ajax_store()
{
    $dom = ZLanguage::getModuleDomain('MultiHook');
    $error = '';
    $return = '';

    // Get parameters from whatever input we need
    // caution: in the mh_user_dynamic.html template the fields are named
    // mhnew_* or mhedit_* but the Ajax function that sends the data uses mh_* to
    // transmit them.
    $aid       = (int)FormUtil::getPassedValue('mh_aid',      -1, 'POST');
    $short     =      FormUtil::getPassedValue('mh_short',    '', 'POST');
    $long      =      FormUtil::getPassedValue('mh_long',     '', 'POST');
    $title     =      FormUtil::getPassedValue('mh_title',    '', 'POST');
    $type      = (int)FormUtil::getPassedValue('mh_type',     0,  'POST');
    $delete    =      FormUtil::getPassedValue('mh_delete',   '', 'POST');
    $language  =      FormUtil::getPassedValue('mh_language', '', 'POST');

    $short    = trim(DataUtil::convertFromUTF8(urldecode($short)));
    $long     = trim(DataUtil::convertFromUTF8(urldecode($long)));
    $title    = trim(DataUtil::convertFromUTF8(urldecode($title)));
    $language = trim($language);

    // get the entry (needed for permission checks)
    if($aid <> -1) {
        $abac = pnModAPIFunc('MultiHook', 'user', 'get',
                             array('aid' => $aid));
    }

    if(!empty($delete)&& ($delete=='1')) {
        if(SecurityUtil::checkPermission('MultiHook::', $abac['short'] . '::' . $abac['aid'], ACCESS_DELETE)) {
            if(pnModAPIFunc('MultiHook', 'admin', 'delete',
                             array('aid' => $aid))) {
                $return = $abac['short'];
            } else {
                $error = __('Database deletion of entry failed', $dom);
            }
        } else {
            $error = __('No permissions for the MultiHook module', $dom);
        }
    } else {
        $mode = '';
        if(!is_array($abac) && SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
            $mode = 'create';
            // check if db entry with this short already exists
            $check_abac = pnModAPIFunc('MultiHook', 'user', 'get',
                                       array('short' => $short));
            if(!is_bool($check_abac)) {
                AjaxUtil::error("'$short' " . __(' already exists in database', $dom));
            }
        }
        if(is_array($abac) && SecurityUtil::checkPermission('MultiHook::', $abac['short'] . '::' . $abac['aid'], ACCESS_EDIT)) {
            $mode = 'update';
        }
        unset($abac);

        if(empty($mode)) {
            $error = __('No permissions for the MultiHook module', $dom);
        } else {
            if(empty($short)) {
                $error = _MH_WRONGPARAMETER_SHORT . '<br />';
            }
            switch($type) {
                case '0': // abbr
                case '1': // acronym
                    if(empty($long)) {
                        $error .= _MH_WRONGPARAMETER_LONG . '<br />';
                    }
                    break;
                case '2': // link
                    if(empty($long)) {
                        $error .= _MH_WRONGPARAMETER_LONG . '<br />';
                    }
                    if(empty($title)) {
                        $error .= _MH_WRONGPARAMETER_TITLE . '<br />';
                    }
                    break;
                case '3': // illegal word
                    break;
                default:
                    $error = _MH_WRONGPARAMETER_TYPE . ' (' . $type . ')<br />';
            }
            AjaxUtil::error($error);

            $aid = pnModAPIFunc('MultiHook', 'admin', $mode,
                                array('aid'      => $aid,
                                      'short'    => $short,
                                      'long'     => $long,
                                      'title'    => $title,
                                      'type'     => $type,
                                      'language' => $language));
            if(is_numeric($aid)) {
                // result is not false, its a aid of the new created or updated entry
                $mhadmin = SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_DELETE);
                $mhshoweditlink = (pnModGetVar('MultiHook', 'mhshoweditlink')==1) ? true : false;
                $haveoverlib = pnModAvailable('overlib');
                $abac = pnModAPIFunc('MultiHook', 'user', 'get',
                                     array('aid' => $aid));
                if(is_array($abac)) {
                    switch($abac['type']) {
                        case '0':  // abbr
                            $return = create_abbr($abac, $mhadmin, $mhshoweditlink, $haveoverlib);
                            break;
                        case '1':  // acronym
                            $return = create_acronym($abac, $mhadmin, $mhshoweditlink, $haveoverlib);
                            break;
                        case '2':  // link
                            $return = create_link($abac, $mhadmin, $mhshoweditlink, $haveoverlib);
                            break;
                        case '3':  // illegal word
                            $return = create_censor($abac, $mhadmin, $mhshoweditlink, $haveoverlib);
                        default:
                            //  we cannot get here, type has been checked before already
                    }
                } else {
                    $error = _MH_ERRORREADINGDATA . ' (aid=' . $aid . ')';
                }
            } else {
                switch($mode) {
                    case 'create':
                        $error = __('Error: entry creation failed', $dom);
                        break;
                    case 'update':
                        $error = __('Database update of entry failed', $dom);
                        break;
                    default:
                        // we should not get here....
                        $error = 'internal error: invalid mode parameter';
                }
            }
        }
    }
    // stop with 400 Bad data if neccesary
    AjaxUtil::error($error);

    // otherwise output result and exit
    AjaxUtil::output($return);
    exit;
}
