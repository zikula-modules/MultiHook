<?php

/**
 * @package Zikula_Utility_Modules
 * @subpackage bbcode
 * @license http://www.gnu.org/copyleft/gpl.html
*/

class MultiHook_HookHandlers extends Zikula_Hook_AbstractHandler
{
    /*
     * filter hook
     * 
     */
    public function uifilter(Zikula_Event $event)
    {
        // who called us
        $caller = $event['caller'];

        $data = ModUtil::apiFunc('MultiHook', 'user', 'transform', 
                                 array('text' => $event->getData()));
        if (SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            PageUtil::addVar('javascript', 'prototype');
            PageUtil::addVar('javascript', 'modules/MultiHook/javascript/multihook.js');
            PageUtil::addVar('stylesheet', ThemeUtil::getModuleStyleSheet('MultiHook'));
            PageUtil::addVar('rawtext', '<script type="text/javascript">var mhloadingText = "' . DataUtil::formatForDisplay(__('Loading data...')) .'"; var mhsavingText = "' . DataUtil::formatForDisplay(__('Saving data...')) . '";</script>');
        }
        $event->setData($data);
        return;     
    }

}