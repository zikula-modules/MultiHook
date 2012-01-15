<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: Admin.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Api_Admin extends Zikula_AbstractApi
{
    /**
     * create a new entry
     * @param $args['short'] short name of the item
     * @param $args['long'] long name of the item
     * @param $args['title'] title of the item
     * @param $args['type'] type of the item: 1=acronym, 0=abbreviation, 2=link
     * @param $args['language'] language of the item
     * @returns int
     * @return id on success, false on failure
     */
    public function create($args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADD)) {
            return LogUtil::registerPermissionError();
        }
    
        // Argument check - make sure that all required arguments are present,
        // if not then set an appropriate error message and return
        if ((!isset($args['short'])) ||
            (!isset($args['long'])) ||
            (!isset($args['title'])) ||
            (!isset($args['type'])) ||
            (!isset($args['language']))) {
            return LogUtil::registerArgsError();
        }
        
        $args['tlong'] = $args['long'];
        unset($args['long']);
    
        $obj = DBUtil::insertObject($args, 'multihook', 'aid');
        if($obj == false) {
            return LogUtil::registerError(__('Error! Could not create new entry.'));
        }
        return $obj['aid'];
    }
    
    /**
     * delete an abbreviation
     * @param $args['aid'] ID of the abbr/acronym/link
     * @returns bool
     * @return true on success, false on failure
     */
    public function delete($args)
    {
        // Security check
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
    
        // Argument check
        if (!isset($args['aid'])) {
            return LogUtil::registerArgsError();
        }
    
        $res = DBUtil::deleteObjectByID ('multihook', (int)$args['aid'], 'aid');
        if($res==false) {
            return LogUtil::registerError(__('Error! Could not delete entry from database.'));
        }

        // Let the calling process know that we have finished successfully
        return true;
    }
    
    /**
     * update an entry
     * @param $args['aid'] the id
     * @param $args['short'] short name
     * @param $args['title'] title
     * @param $args['long'] long name
     * @param $args['type'] type
     * @param $args['language'] language
     */
    public function update($args)
    {
        if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }
    
        // Argument check
        if ((!isset($args['aid'])) ||
            (!isset($args['short'])) ||
            (!isset($args['title'])) ||
            (!isset($args['long'])) ||
            (!isset($args['type'])) ||
            (!isset($args['language']))) {
            return LogUtil::registerArgsError();
        }

        $args['tlong'] = $args['long'];
        unset($args['long']);
    
        $res = DBUtil::updateObject($args, 'multihook', '', 'aid');
        if($res == false) {
            return LogUtil::registerError(__('Error! Could not save changes to database.'));
        }
        return $args['aid'];
    }
    
    /**
     * collectneedles
     * scans the pnneedleapi folder for needles and stores them in a module var
     *
     *@params none
     *@returns array of needles
     */
    public function collectneedles()
    {
        $needles = array();
    
        $modtypes = array(2 => 'modules', 3 => 'system');
        // get an array with modinfos of all active modules
        $allmods = ModUtil::getAllMods();
        if(is_array($allmods) && count($allmods)>0) {
            foreach($allmods as $mod) {
                $needledir = $modtypes[$mod['type']] . '/' . $mod['directory'] . '/lib/' . $mod['directory'] . '/Needles/';
                if(file_exists($needledir) && is_readable($needledir)) {
                    $dh = opendir($needledir);
                    if($dh) {
                        while($file = readdir($dh)) {
                            if((is_file($needledir . $file)) &&
                                    ($file != '.') &&
                                    ($file != '..') &&
                                    ($file != '.svn') &&
                                    ($file != 'index.html')) {

                                $needlename = str_replace('.php', '', $file);
                                $needleClass = $mod['name'].'_Needles_'.$needlename;
                                $needleObj = new $needleClass($this);
                                
                                $needleinfo = $needleObj->info(); // ModUtil::exec($mod['name'], $needle, 'info');

                                $needleinfo['module']  = $mod['name'];
                                $needleinfo['builtin'] = ($mod['name']=='MultiHook') ? true : false;

                                $needles[] = $needleinfo;
                            }
                        }
                        closedir($dh);
                    }
                }
            }
            // sort needles by needle name
            uasort($needles, 'cmp_needleorder');
        }
        // store the needles array now
        $this->setVar('needles', $needles);
        return $needles;
    }
    
    /**
     * get available admin panel links
     *
     * @author Mark West
     * @return array array of admin links
     */
    public function getlinks()
    {
        $links = array();
        if (SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('MultiHook', 'admin', 'main'), 'text' => __('Settings'));
            $links[] = array('url' => ModUtil::url('MultiHook', 'admin', 'edit', array('aid' => -1)), 'text' => __('Create new item'));
            $links[] = array('url' => ModUtil::url('MultiHook', 'admin', 'view', array('filter' => 0)), 'text' => __('Abbreviations list'), 'title' => __('Abbreviations list'));
            $links[] = array('url' => ModUtil::url('MultiHook', 'admin', 'view', array('filter' => 1)), 'text' => __('Acronyms list'), 'title' => __('Acronyms list'));
            $links[] = array('url' => ModUtil::url('MultiHook', 'admin', 'view', array('filter' => 2)), 'text' => __('Links list'), 'title' => __('Links list'));
            $links[] = array('url' => ModUtil::url('MultiHook', 'admin', 'view', array('filter' => 3)), 'text' => __('Censored words list'), 'title' => __('Censored words list'));
            $links[] = array('url' => ModUtil::url('MultiHook', 'admin', 'viewneedles'), 'text' => __('Needles list'), 'title' => __('Needles list'));
        }
        return $links;
    }
}

/**
 * sorting needles by module name
 *
 */
function cmp_needleorder ($a, $b)
{
    return $a['module'] > $b['module'];
}
    
