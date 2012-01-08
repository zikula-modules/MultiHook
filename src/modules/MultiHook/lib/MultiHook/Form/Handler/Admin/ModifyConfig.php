<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: ModifyConfig.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Form_Handler_Admin_ModifyConfig extends Zikula_Form_AbstractHandler
{

    var $id;

    public function initialize(Zikula_Form_View $view)
    {
        $this->id = (int)FormUtil::getPassedValue('id', 0, 'GETPOST');
        $view->caching = false;
        $view->add_core_data();
        return true;
    }


    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError(System::getVar('entrypoint', 'index.php'));
        }  
        if ($args['commandName'] == 'submit') {
            if (!$view->isValid()) {
                return false;
            }
            $data = $view->getValues();
            $this->setVar('abacfirst',          $data['abacfirst']);
            $this->setVar('mhincodetags',       $data['mhincodetags']);
            $this->setVar('mhlinktitle',        $data['mhlinktitle']);
            $this->setVar('mhreplaceabbr',      $data['mhreplaceabbr']);
            $this->setVar('mhshoweditlink',     $data['mhshoweditlink']);
            $this->setVar('itemsperpage',       $data['itemsperpage']);
            $this->setVar('externallinkclass',  $data['externallinkclass']);
            $this->setVar('mhbrutalcensor',     $data['mhbrutalcensor']);
            $this->setVar('mhrelaxedcensoring', $data['mhrelaxedcensoring']);

            LogUtil::registerStatus(__('Done! Saved your settings changes.'));
        }
        return true;
    }

}
