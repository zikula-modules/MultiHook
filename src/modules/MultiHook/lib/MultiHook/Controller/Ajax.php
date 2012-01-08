<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: Ajax.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Controller_Admin extends Zikula_Controller
{
    public function read()
    {
        $error = '';
        $return = '';
    
        $aid = (int)FormUtil::getPassedValue('mh_aid', -1, 'POST');
        $abac = ModUtil::apiFunc('MultiHook', 'user', 'get',
                                 array('aid' => $aid));
        if(is_array($abac)) {
            AjaxUtil::output($abac);
            exit;
        } else {
            AjaxUtil::error(__('Error! Could not read data.') . ' (aid=' . $aid . ')', '404 Not found');
        }
        // we should never get here
    }
    
    public function store()
    {
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
    
        $short    = trim(urldecode($short));
        $long     = trim(urldecode($long));
        $title    = trim(urldecode($title));
        $language = trim($language);
    
        $abac = null;
        
        // get the entry (needed for permission checks)
        if($aid <> -1) {
            $abac = ModUtil::apiFunc('MultiHook', 'user', 'get',
                                 array('aid' => $aid));
        }
    
        if(!empty($delete)&& ($delete=='1')) {
            if(SecurityUtil::checkPermission('MultiHook::', $abac['short'] . '::' . $abac['aid'], ACCESS_DELETE)) {
                if(ModUtil::apiFunc('MultiHook', 'admin', 'delete',
                                 array('aid' => $aid))) {
                    $return = $abac['short'];
                } else {
                    $error = __('Error! Could not delete entry from database.');
                }
            } else {
                $error = __('Error! Permissions not granted for the MultiHook module.');
            }
        } else {
            $mode = '';
            if(!is_array($abac) && SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
                $mode = 'create';
                // check if db entry with this short already exists
                $check_abac = ModUtil::apiFunc('MultiHook', 'user', 'get',
                                           array('short' => $short));
                if(!is_bool($check_abac)) {
                    AjaxUtil::error("'$short' " . __(' already exists in database.'));
                }
            }
            if(is_array($abac) && SecurityUtil::checkPermission('MultiHook::', $abac['short'] . '::' . $abac['aid'], ACCESS_EDIT)) {
                $mode = 'update';
            }
            unset($abac);
    
            if(empty($mode)) {
                $error = __('Error! Permissions not granted for the MultiHook module.');
            } else {
                if(empty($short)) {
                    $error = __('no short text') . '<br />';
                    
                }
                switch($type) {
                    case '0': // abbr
                    case '1': // acronym
                        if(empty($long)) {
                            $error .= __('no long text or (in case of a link) no URL') . '<br />';
                        }
                        break;
                    case '2': // link
                        if(empty($long)) {
                            $error .= __('no long text or (in case of a link) no URL') . '<br />';
                        }
                        if(empty($title)) {
                            $error .= __('no title') . '<br />';
                        }
                        break;
                    case '3': // censored word
                        break;
                    default:
                        $error = __('no type') . ' (' . $type . ')<br />';
                }
                AjaxUtil::error($error);
    
                $aid = ModUtil::apiFunc('MultiHook', 'admin', $mode,
                                    array('aid'      => $aid,
                                          'short'    => $short,
                                          'long'     => $long,
                                          'title'    => $title,
                                          'type'     => $type,
                                          'language' => $language));
                if(is_numeric($aid)) {
                    // result is not false, its a aid of the new created or updated entry
                    $mhadmin = SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_DELETE);
                    $mhshoweditlink = (ModUtil::getVar('MultiHook', 'mhshoweditlink')==1) ? true : false;
                    $haveoverlib = ModUtil::available('overlib');
                    $abac = ModUtil::apiFunc('MultiHook', 'user', 'get',
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
                            case '3':  // censored word
                                $return = create_censor($abac, $mhadmin, $mhshoweditlink, $haveoverlib);
                            default:
                                //  we cannot get here, type has been checked before already
                        }
                    } else {
                        $error = __('Error! Could not read data.') . ' (aid=' . $aid . ')';
                    }
                } else {
                    switch($mode) {
                        case 'create':
                            $error = __('Error! Could not create new entry.');
                            break;
                        case 'update':
                            $error = __('Error! Could not save changes to database.');
                            break;
                        default:
                            // we should not get here....
                            $error = __('Internal error! Invalid mode parameter.');
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
}