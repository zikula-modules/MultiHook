<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: Installer.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

Class MultiHook_Installer extends Zikula_AbstractInstaller 
{
    /**
     * initialise the MultiHook module
     */
    public function install()
    {
        // create the MultiHook table
        // the table definition itself is done in pntables.php
        if (!DBUtil::createTable('multihook')) {
            return false;
        }
    
        // Set up module variables
        $this->setVar('itemsperpage', 20);
        $this->setVar('abacfirst', 0);
        $this->setVar('mhincodetags', 0);
        $this->setVar('mhlinktitle', 0);
        $this->setVar('mhreplaceabbr', 0);
        $this->setVar('mhshoweditlink', 1);
        $this->setVar('mhbrutalcensor', 0);
        $this->setVar('mhrelaxedcensoring', 0);
    
        // collect the needles
        // Force loading of adminapi with 3rd parameter set to true. This loads the api although
        // the module is not really available yet. You as the module author are responsible
        // for any side effects now, eg. when calling functions that access database tables or use
        // module vars that have not been set yet.
//ModUtil::loadApi('MultiHook', 'admin', true);
//ModUtil::apiFunc('MultiHook', 'admin', 'collectneedles');
    
//        EventUtil::registerPersistentModuleHandler('MultiHook', 'module.multihook.getNeedles', array('MultiHook_Listener', 'getNeedles'));
//      EventUtil::registerPersistentModuleHandler('Clip', 'module.content.getTypes', array('Clip_EventHandler_Listeners', 'getTypes'));

        
        // create hook
        HookUtil::registerProviderBundles($this->version->getHookProviderBundles());
        
        // Initialisation successful
        return true;
    }
    
    /**
     * upgrade the module from an old version
     */
    public function upgrade($oldversion)
    {
        // Upgrade dependent on old version number
        // There is no
        // break;
        // at the end of each case which means that if you start with eg. version 1.1 all
        // necessary upgrade steps up to the recent version are done.
        // The recent version usually does not appear as a case here. Same for the default case
    
        // change the database. DBUtil + ADODB detect the changes on their own
        // and perform all necessary steps without help from the module author
        //if (!DBUtil::changeTable('multihook')) {
        //    return LogUtil::registerError(__('Error! Upgrade of multihook table to failed.'));
        //}
        switch($oldversion) {
            case '5.2':
                // future upgrade routines
                // create hook
                HookUtil::registerProviderBundles($this->version->getHookProviderBundles());
                
                // remove table prefix
                $connection = Doctrine_Manager::getInstance()->getConnection('default');
                $sqlStatements = array();
                $oldTable = DBUtil::getLimitedTablename('multihook');
                if ($oldTable <> 'multihook') {
                    $sqlStatements[] = 'RENAME TABLE ' . $oldTable . " TO multihook";
                }
                $sqlStatements[] = "ALTER TABLE `multihook` 
                                          CHANGE `pn_aid`      `aid` INT( 11 ) NOT NULL AUTO_INCREMENT,
                                          CHANGE `pn_short`    `short` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                                          CHANGE `pn_long`     `tlong` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                                          CHANGE `pn_title`    `title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
                                          CHANGE `pn_type`     `type` TINYINT( 4 ) NOT NULL DEFAULT '0',
                                          CHANGE `pn_language` `language` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
                
                 foreach ($sqlStatements as $sql) {
                 $stmt = $connection->prepare($sql);
                     try {
                         $stmt->execute();
                     } catch (Exception $e) {
                         // trap and toss exceptions if you need to.
                        return LogUtil::registerError(__("Failed: $sql"));
                     }   
                 }

            default:
        }
    
        // collecting needles
        // force loading of adminapi
        ModUtil::loadApi('MultiHook', 'admin', true);
        ModUtil::apiFunc('MultiHook', 'admin', 'collectneedles');
        // clear compiled templates. This function is new in Zikula and ensures that after
        // an upgrade the new templates will be used without the need to manually
        // clear the compiled templates.
        // minor drawback: this clears ALL compiled templates for ALL modules
        ModUtil::apiFunc('view', 'user', 'clear_compiled');
    
        return true;
    }
    
    /**
     * delete the MultiHook module
     */
    public function uninstall()
    {
        // drop the table
        if (!DBUtil::dropTable('multihook')) {
            return LogUtil::registerError(__('Error! Could not delete table from database.'));
        }
    
        // Remove module variables
        // using ModUtil::delVar with only one parameter (the module name) automatically
        // deletes all existing vars in one call
        $this->delVar();
    
        // remove hook
        HookUtil::unregisterHookProviderBundles($this->version);
    
        // Deletion successful
        return true;
    }
    
}
