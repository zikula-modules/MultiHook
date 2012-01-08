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

class MultiHook_Controller_User extends Zikula_AbstractController
{
    public function postInitialize()
    {
        $this->view->add_core_data();
    }

    /**
     * the main function
     *
     *@params $filter (int)
     *@params $startnum (int)
     */
    public function main()
    {
        // Get parameters from whatever input we need
        $startnum = (int)FormUtil::getPassedValue('startnum', 0, 'GETPOST');
        $filter   = (int)FormUtil::getPassedValue('filter', -1, 'GETPOST');
    
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError('index.php');
        }
    
        // no censor!
        if($filter>=0 && $filter<=2) {
            $abacs = ModUtil::apiFunc('MultiHook', 'user', 'getall',
                                      array('startnum' => $startnum,
                                            'filter'   => $filter,
                                            'numitems' => $this->getVar('itemsperpage')));
            $abacscount = ModUtil::apiFunc('MultiHook', 'user', 'countitems', 
                                           array('filter' => $filter));
        } else {
            $abacs = array();
            $abacscount = 0;
            $filter = -1;
        }
        $titles = array( __('Abbreviations list'),
                         __('Acronyms list'),
                         __('Links list'));
    
        // Create output object
        $this->view->assign('abacs', $abacs);
        $this->view->assign('title', $titles[$filter]);
        $this->view->assign('filter', $filter);
        $this->view->assign('abacscount', $abacscount );
        return $this->view->fetch('mh_user_main.html');
    }
}
