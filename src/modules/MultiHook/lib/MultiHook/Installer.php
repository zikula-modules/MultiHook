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
        if (!DBUtil::changeTable('multihook')) {
            return LogUtil::registerError(__('Error! Upgrade of multihook table to failed.'));
        }
        switch($oldversion) {
            case '5.2':
                // future upgrade routines
                // create hook
                HookUtil::registerHookProviderBundles($this->version);
                // remove column prefixes
                // rename field 'long' to 'tlong' as 'long' is a reserved keyword
                // internally, 'long' will be used, during load the mapping will be done accordingly
                DBUtil::renameColumn('multihook', 'pn_aid',      'aid');
                DBUtil::renameColumn('multihook', 'pn_short',    'short');
                DBUtil::renameColumn('multihook', 'pn_long',     'tlong'); // caution!!
                DBUtil::renameColumn('multihook', 'pn_title',    'title');
                DBUtil::renameColumn('multihook', 'pn_type',     'type');
                DBUtil::renameColumn('multihook', 'pn_language', 'language');
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
