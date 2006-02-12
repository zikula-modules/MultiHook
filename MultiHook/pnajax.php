<?php

include_once('modules/MultiHook/common.php');

function MultiHook_ajax_read()
{
    $error = '';
    $return = '';
    
    $aid = pnVarCleanFromInput('mh_aid');
    $abac = pnModAPIFunc('MultiHook', 'user', 'get', 
                         array('aid' => $aid));
    if(is_array($abac)) {
        $return = implode('$', $abac);
    } else {
        $error = _MH_ERRORREADINGDATA . ' (aid=' . $aid . ')';
    }

    mh_ajaxerror($error);
    
    echo $return;  
    exit;
}

function MultiHook_ajax_store()
{
    $error = '';
    $return = '';

    list($aid,
         $short, 
         $long, 
         $title, 
         $type,
         $delete,
         $language) = pnVarCleanFromInput('mh_aid',
                                          'mh_short', 
                                          'mh_long',
                                          'mh_title',
                                          'mh_type',
                                          'mh_delete',
                                          'mh_language');

    // check if aid exists 
    $abac = pnModAPIFunc('MultiHook', 'user', 'get', 
                         array('aid' => $aid));
    $mode = '';
    if(is_array($abac) && pnSecAuthAction(0, 'MultiHook::', $abac['short'] . '::' . $abac['aid'], ACCESS_EDIT)) {
        $mode = 'update';
        if(!empty($delete)&& ($delete=='1')) {
            $mode = 'delete';
        }
    }
    if(!is_array($abac) && pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_ADD)) {
        $mode = 'create';
    }
    if(!empty($mode)) {
    
        pnModLangLoad('MultiHook', 'admin');
        
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
                if(empty($long) || (pnVarValidate($long, 'url')==false)) {
                    $error .= _MH_WRONGPARAMETER_LONG . '<br />';
                }
                if(empty($title)) {
                    $error .= _MH_WRONGPARAMETER_TITLE . '<br />';
                }
                break;
            default:
                $error = _MH_WRONGPARAMETER_TYPE . ' (' . $type . ')<br />';
        }
        
        $aid = pnModAPIFunc('MultiHook', 'admin', $mode,
                            array('aid'      => $aid,
                                  'short'    => $short,
                                  'long'     => utf8_decode($long),
                                  'title'    => utf8_decode($title),
                                  'type'     => $type,
                                  'language' => $language));
        if(!is_bool($aid)) {
            // result is not false, its a aid of the new created or updated entry
            pnModLangLoad('MultiHook','admin');
            $mhadmin = pnSecAuthAction(0, 'MultiHook::', '.*', ACCESS_ADMIN);
            $mhshoweditlink = (pnModGetVar('MultiHook', 'mhshoweditlink')==1) ? true : false;
            $haveoverlib = pnModAvailable('overlib');
            $abac = pnModAPIFunc('MultiHook', 'user', 'get',
                                 array('aid' => $aid));
            if(is_array($abac)) {
                switch($abac['type']) {
                    case '0':  // abbr
                        $return = create_abbr($abac['aid'], $abac['short'], $abac['long'], $abac['language'], $mhadmin, $mhshoweditlink, $haveoverlib);
                        break;
                    case '1':  // acronym
                        $return = create_acronym($abac['aid'], $abac['short'], $abac['long'], $abac['language'], $mhadmin, $mhshoweditlink, $haveoverlib);
                        break;
                    case '2':  // link
                        $return = create_link($abac['aid'], $abac['short'], $abac['long'], $abac['title'], $abac['language'], $mhadmin, $mhshoweditlink, $haveoverlib);
                        break;
                    default:
                        // why are we here?
                }
            } else {
                $error = _MH_ERRORREADINGDATA . ' (aid=' . $aid . ')';
            }
        } else {
            switch($mode) {
                case 'create':
                    $error = _MH_CREATEFAILED;
                    break;
                case 'update':
                    $error = _MH_UPDATEFAILED;
                    break;
                case 'delete':
                    $return = $abac['short'];
                    break;
                default:
                    $error = 'internal error: invalid mode parameter';
            }
        }
    } else {
        $error = _MH_NOAUTH;
    }
    
    mh_ajaxerror($error);
    
    echo $return;
    exit;
}

?>