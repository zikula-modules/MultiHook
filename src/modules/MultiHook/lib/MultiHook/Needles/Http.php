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
 
class MultiHook_Needles_Http extends Zikula_AbstractHelper
{
    /**
     * wave needle
     * @param $args['nid'] needle id
     * @return array()
     */

    public function info()
    {
        $info = array('info'          => 'http://www.example.com',   // possible needles
                      'description'   => $this->__('Create a clickable link from an url'),
                      'inspect'       => true,
                      'needle'        => array('http://', 'https://', 'ftp://', 'mailto://'),
                      'casesensitive' => false);
        return $info;
    }

    /**
     * changeset needle
     * @param $args['nid'] needle id
     * @param $args['needle'] the needle itself
     * @return array()
     */
    public static function needle($args)
    {
        // simple replacement, no need to cache anything
        if (isset($args['nid']) && !empty($args['nid'])) {
            $url = DataUtil::formatForDisplay($args['needle'] . $args['nid']);
    
            $extclass = '';
            if(stristr(pnGetBaseURL(), $url) === false) {
                $externallinkclass =pnModGetVar('MultiHook', 'externallinkclass', '');
                if(!empty($externallinkclass)) {
                    $extclass = "class=\"$externallinkclass\"";
                }
            }
    
            $result = '<a ' . $extclass . ' href="' . $url . '">' . $url . '</a>'; 
            return $result;
        } 
        return $args['nid'];   
    }
}
