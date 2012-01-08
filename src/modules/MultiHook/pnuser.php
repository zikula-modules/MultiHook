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

/**
 * the main function
 *
 *@params $filter (int)
 *@params $startnum (int)
 */
function MultiHook_user_main()
{
    $dom = ZLanguage::getModuleDomain('MultiHook');

    // Get parameters from whatever input we need
    $startnum = (int)FormUtil::getPassedValue('startnum', 0, 'GETPOST');
    $filter   = (int)FormUtil::getPassedValue('filter', -1, 'GETPOST');

    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_READ)) {
        return LogUtil::registerPermissionError('index.php');
    }

    // no censor!
    if($filter>=0 && $filter<=2) {
        $abacs = pnModAPIFunc('MultiHook',
                              'user',
                              'getall',
                              array('startnum' => $startnum,
                                    'filter'   => $filter,
                                    'numitems' => pnModGetVar('MultiHook',
                                                              'itemsperpage')));
        $abacscount = pnModAPIFunc('MultiHook', 'user', 'countitems', array('filter' => $filter));
    } else {
        $abacs = array();
        $abacscount = 0;
        $filter = -1;
    }
    $titles = array( __('Abbreviations list', $dom),
                     __('Acronyms list', $dom),
                     __('Links list', $dom));

    // Create output object
    $render = & pnRender::getInstance('MultiHook', false, null, true);
    $render->assign('abacs', $abacs);
    $render->assign('title', $titles[$filter]);
    $render->assign('filter', $filter);
    $render->assign('abacscount', $abacscount );
    return $render->fetch('mh_user_main.html');
}
