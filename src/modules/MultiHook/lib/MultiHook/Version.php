<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: Version.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array();
        $meta['name']             = 'MultiHook';
        $meta['version']          = '5.3.0';
        $meta['description']      = __('Provides a hooked module that generates XHTML tags for abbreviations, acronyms and automatically-generated links in content from other modules, and enables censorship of specified words and phrases in the content of modules to which it is hooked.');
        $meta['displayname']      = __('MultiHook content filters');
        //! module url should be lowercase without spaces and different to displayname
        $meta['url']              = __('multihook');
        $meta['credits']          = 'docs/credits.txt';
        $meta['help']             = 'docs/help.txt';
        $meta['changelog']        = 'docs/changelog.txt';
        $meta['license']          = 'docs/license.txt';
        $meta['author']           = 'Frank Schummertz';
        $meta['contact']          = 'frank.schummertz@landseer-stuttgart.de';
        $meta['securityschema']   = array('MultiHook::' => 'Shorttext::$ID' );
        $meta['capabilities']     = array(HookUtil::PROVIDER_CAPABLE => array('enabled' => true));        
        
        return $meta;
    }

    protected function setupHookBundles()
    {
        $bundle = new Zikula_HookManager_ProviderBundle($this->name, 'provider.filter_hooks.multihook.multihook', 'filter_hooks', $this->__('MultiHook Providers'));
        $bundle->addServiceHandler('filter', 'MultiHook_HookHandler', 'filter', 'multihook.multihook');
        // add other hook handlers as needed

        $this->registerHookProviderBundle($bundle);
    }

}