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

class MultiHook_Handler_Edit extends Zikula_Form_AbstractHandler
{
    private $aid;
    
    private $abac;

    function initialize(Zikula_Form_View $view)
    {
        $this->aid = (int)FormUtil::getPassedValue('aid', -1, 'GETPOST', FILTER_SANITIZE_NUMBER_INT);
        $view->caching = false;
        $view->add_core_data();
            
        if(($this->aid==-1) ) {
            $abac = new MultiHook_Entity_Abac();
            $abac->setLanguage(ZLanguage::getLanguageCode());
        } else {
            $abac = ModUtil::apiFunc('MultiHook', 'user', 'get',
                                     array('aid'   => $this->aid,
                                           'array' => false));
        }

        $items = array( array('text' => __('Abbreviation'),  'value' => 0),
                        array('text' => __('Acronym'),       'value' => 1),
                        array('text' => __('Link'),          'value' => 2),
                        array('text' => __('Censored word'), 'value' => 3) );
 
        $view->assign('items', $items)
             ->assign('abac', $abac->toArray());

        $this->abac = $abac;
        
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
            
            $abac = $this->abac;

            if(isset($data['mh_delete']) && ($data['mh_delete']==true) ) {
                ModUtil::apiFunc('MultiHook', 'admin', 'delete',
                                 array('aid' => $data['aid']));
                return System::redirect(ModUtil::url('MultiHook', 'admin', 'view', array('filter' => $data['mh_type'])));
            }

            // no deletion, further checks needed
            if(empty($data['mh_shortform'])) {
                $ifield = $view->getPluginById('mh_shortform');
                $ifield->setError(DataUtil::formatForDisplay(__('missing short text')));
                $ok = false;
            }
            if(empty($data['mh_longform']) && ($data['mh_type']<>3)) {
                $ifield = & $view->getPluginById('mh_longform');
                $ifield->setError(DataUtil::formatForDisplay(__('missing long text')));
                $ok = false;
            }
            if(($data['mh_type']<0) || ($data['mh_type']>3)) {
                $ifield = & $view->pnFormGetPluginById('mh_type');
                $ifield->setError(DataUtil::formatForDisplay(__('missing type text')));
                $ok = false;
            }
            if($data['mh_type']==2 && empty($data['mh_title'])) {
                $ifield = & $view->getPluginById('mh_title');
                $ifield->setError(DataUtil::formatForDisplay(__('missing title text')));
                $ok = false;
            }
            
            if(!$ok) {
                return false;
            }

            if(empty($data['mh_language'])) {
                $data['mh_language'] = 'All';
            }
            
            $abac->setLongform($data['mh_longform']);
            $abac->setShortform($data['mh_shortform']);
            $abac->setTitle($data['mh_title']);
            $abac->setType($data['mh_type']);
            $abac->setLanguage($data['mh_language']);

            $this->entityManager->persist($abac);
            $this->entityManager->flush();
        }
        return System::redirect(ModUtil::url('MultiHook', 'admin', 'view', array('filter' => $data['mh_type'])));
    }

}
