<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: http.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Listeners_http extends Zikula_AbstractPlugin
{
    
    /**
     * http needle
     * @param $args['nid'] needle id
     * @param $args['needle'] the needle itself
     * @return array()
     */
    public function getNeedles($args)
    {
/*
        // simple replacement, no need to cache anything
        if (isset($args['nid']) && !empty($args['nid'])) {
            $url = DataUtil::formatForDisplay($args['needle'] . $args['nid']);
    
            $extclass = '';
            if(stristr(System::getBaseUrl(), $url) === false) {
                $externallinkclass = ModUtil::getVar('MultiHook', 'externallinkclass', '');
                if(!empty($externallinkclass)) {
                    $extclass = "class=\"$externallinkclass\"";
                }
            }
    
            $result = '<a ' . $extclass . ' href="' . $url . '">' . $url . '</a>'; 
            return $result;
        } 
        return $args['nid'];   
*/
    }
        
    protected function getMeta()
    {
        $meta = array(/* 'inspect'       => true,
                      'needle'        => array('http://', 'https://', 'ftp://', 'mailto://'),
                      'function'      => 'http',
                      'casesensitive' => false,*/
                      'version'       => '1.0.0',
                      'description'   => $this->__('Turns an url into a clickable link'),
                      'displayname'   => $this->__('http needle')); 
 
        return $meta;
    }
    
}
