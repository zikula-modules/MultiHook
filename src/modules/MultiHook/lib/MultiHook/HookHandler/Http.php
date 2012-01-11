<?php
/**
 * Multihook http needle
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: Edit.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_HookHandler_http extends Zikula_Hook_AbstractHandler
{
    public static function filter(Zikula_FilterHook $hook)
    {
        $data = $hook->getData();
        $newdata = '*HTTPNEEDLE*' . $data . '*HTTPNEEDLE*';
        $hook->setData($newdata);
    }
}
