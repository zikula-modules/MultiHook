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

class MultiHook_admin_edithandler
{
    var $aid;

    function initialize(&$pnRender)
    {
        $dom = ZLanguage::getModuleDomain('MultiHook');
        $this->aid = (int)FormUtil::getPassedValue('aid', -1, 'GETPOST');
        $pnRender->caching = false;
        $pnRender->add_core_data();
            
        if(($this->aid==-1) ) {
            $abac = array( 'aid'   => -1,
                           'short' => '',
                           'long'  => '',
                           'title' => '',
                           'type'  => 0,
                           'language' => ZLanguage::getLanguageCode() );
        } else {
            $abac = pnModAPIFunc('MultiHook',
                                 'user',
                                 'get',
                                 array('aid' => $this->aid));
        
            if ($abac == false) {
                return LogUtil::registerError(__('Error! No such item exists.', $dom), pnModURL('MultiHook', 'admin', 'main'));
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

        $items = array( array('text' => __('Abbreviation', $dom), 'value' => 0),
                        array('text' => __('Acronym', $dom),      'value' => 1),
                        array('text' => __('Link', $dom),         'value' => 2),
                        array('text' => __('Censored word', $dom),  'value' => 3) );
 
        $pnRender->assign('items', $items); // Supply items
        $pnRender->assign('abac', $abac);

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
                    LogUtil::registerStatus(__('Done! Entry deleted.', $dom));
                } else {
                    LogUtil::registerError(__('Error! Could not delete entry from database.', $dom));
                }
                return pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter' => $data['type'])));
            }

            // no deletion, further checks needed
            if(empty($data['short'])) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_short');
                $ifield->setError(DataUtil::formatForDisplay(__('missing short text', $dom)));
                $ok = false;
            }
            if(empty($data['long']) && ($data['type']<>3)) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_long');
                $ifield->setError(DataUtil::formatForDisplay(__('missing long text', $dom)));
                $ok = false;
            }
            if(($data['type']<0) || ($data['type']>3)) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_type');
                $ifield->setError(DataUtil::formatForDisplay(__('missing type text', $dom)));
                $ok = false;
            }
            if($data['type']==2 && empty($data['title'])) {
                $ifield = & $pnRender->pnFormGetPluginById('mh_title');
                $ifield->setError(DataUtil::formatForDisplay(__('missing title text', $dom)));
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
                if(pnModAPIFunc('MultiHook', 'admin', 'create', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(__('Done! Entry created.', $dom));
                } else {
                    LogUtil::registerError(__('Error! Could not create entry in database.', $dom));
                }
            } else {
                if(pnModAPIFunc('MultiHook', 'admin', 'update', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(__('Done! Entry updated.', $dom));
                } else {
                    LogUtil::registerError(__('Error! Could not save changes to database.', $dom));
                }
            }

        }
        return pnRedirect(pnModURL('MultiHook', 'admin', 'view', array('filter' => $data['type'])));
    }

}
