<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: Edit.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Form_Handler_Admin_Edit extends Zikula_Form_AbstractHandler
{
    var $aid;

    function initialize(Zikula_Form_View $view)
    {
        $this->aid = (int)FormUtil::getPassedValue('aid', -1, 'GETPOST');
        $view->caching = false;
        $view->add_core_data();
            
        if(($this->aid==-1) ) {
            $abac = array('aid'      => -1,
                          'short'    => '',
                          'long'     => '',
                          'title'    => '',
                          'type'     => 0,
                          'language' => ZLanguage::getLanguageCode(),
                          'delete'   => false);
        } else {
            $abac = ModUtil::apiFunc('MultiHook', 'user', 'get',
                                     array('aid' => $this->aid));
        
            if ($abac == false) {
                return LogUtil::registerError(__('Error! No such item exists.'), ModUtil::url('MultiHook', 'admin', 'main'));
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
                return LogUtil::registerPermissionError(ModUtil::url('MultiHook','admin','main'));
            }
        
        }

        $items = array( array('text' => __('Abbreviation'),  'value' => 0),
                        array('text' => __('Acronym'),       'value' => 1),
                        array('text' => __('Link'),          'value' => 2),
                        array('text' => __('Censored word'), 'value' => 3) );
 
        $view->assign('items', $items); // Supply items
        $view->assign('abac', $abac);

        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(System::getVar('entrypoint', 'index.php'));
        }  
        if ($args['commandName'] == 'submit') {
            $ok = $view->isValid();

            $data = $view->getValues();
            $data['aid'] = $this->aid;

            if(isset($data['mh_delete']) && ($data['mh_delete']==true) ) {
                // The API function is called
                if (ModUtil::apiFunc('MultiHook', 'admin', 'delete',
                                     array('aid' => $data['aid']))) {
                    // Success
                    LogUtil::registerStatus(__('Done! Entry deleted.'));
                } else {
                    LogUtil::registerError(__('Error! Could not delete entry from database.'));
                }
                return System::redirect(ModUtil::url('MultiHook', 'admin', 'view', array('filter' => $data['type'])));
            }

            // no deletion, further checks needed
            if(empty($data['short'])) {
                $ifield = & $view->getPluginById('mh_short');
                $ifield->setError(DataUtil::formatForDisplay(__('missing short text')));
                $ok = false;
            }
            if(empty($data['long']) && ($data['type']<>3)) {
                $ifield = & $view->getPluginById('mh_long');
                $ifield->setError(DataUtil::formatForDisplay(__('missing long text')));
                $ok = false;
            }
            if(($data['type']<0) || ($data['type']>3)) {
                $ifield = & $view->pnFormGetPluginById('mh_type');
                $ifield->setError(DataUtil::formatForDisplay(__('missing type text')));
                $ok = false;
            }
            if($data['type']==2 && empty($data['title'])) {
                $ifield = & $view->getPluginById('mh_title');
                $ifield->setError(DataUtil::formatForDisplay(__('missing title text')));
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
                if(ModUtil::apiFunc('MultiHook', 'admin', 'create', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(__('Done! Entry created.'));
                } else {
                    LogUtil::registerError(__('Error! Could not create entry in database.'));
                }
            } else {
                if(ModUtil::apiFunc('MultiHook', 'admin', 'update', $data) <> false) {
                    // Success
                    LogUtil::registerStatus(__('Done! Entry updated.'));
                } else {
                    LogUtil::registerError(__('Error! Could not save changes to database.'));
                }
            }

        }
        return System::redirect(ModUtil::url('MultiHook', 'admin', 'view', array('filter' => $data['type'])));
    }

}
