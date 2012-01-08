<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: User.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Footer extends Zikula_AbstractBase
{
    public static function includeFooter(Zikula_Event $event)
    {
        if(SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
            PageUtil::addVar('stylesheet', ThemeUtil::getModuleStyleSheet('MultiHook'));
            PageUtil::addVar('javascript', 'javascript/ajax/prototype.js');
            PageUtil::addVar('javascript', 'javascript/ajax/effects.js');
            PageUtil::addVar('javascript', 'javascript/ajax/dragdrop.js');
            PageUtil::addVar('javascript', 'modules/MultiHook/javascript/multihook.js');
            PageUtil::addVar('rawtext', '<script type="text/javascript">var mhloadingText = "' . DataUtil::formatForDisplay(__('Loading data...')) .'"; var mhsavingText = "' . DataUtil::formatForDisplay(__('Saving data...')) . '";</script>');
            
            $out = $event->getData();
            $view = Zikula_View::getInstance('MultiHook');
            $view->assign('userlang', ZLanguage::getLanguageCode());
            $view->assign('modinfo', ModUtil::getInfo(ModUtil::getIdFromName('MultiHook')));
            $out = str_replace('</body>', $view->fetch('mh_dynamic_hiddenform.html').'</body>', $out);
            $event->setData($out);
        }

    }
}
