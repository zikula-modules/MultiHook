<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: changeset.php 221 2009-12-09 07:46:02Z herr.vorragend $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

class MultiHook_Needles_Changeset extends Zikula_AbstractHelper
{
    /**
     * wave needle
     * @param $args['nid'] needle id
     * @return array()
     */

    public function info()
    {
        $info = array('info'          => 'CHANGESET-{projectname-changesetid}',   // possible needles
                      'description'   => $this->__('Link to a changeset on code.zikula.org'),
                      'casesensitive' => true,
                      'needle'        => 'CHANGESET',
                      'inspect'       => false);
        return $info;
    }

    /**
     * changeset needle
     * @param $args['nid'] needle id
     * @return array()
     */
    public static function needle($args)
    {
        $dom = ZLanguage::getModuleDomain('MultiHook');
        // simple replacement, no need to cache anything
        if (isset($args['nid']) && !empty($args['nid'])) {
            if (substr($args['nid'], 0, 1) != '-') {
                $args['nid'] =  '-' . $args['nid'];
            }
            $parts = explode('-', $args['nid']);
            $project = DataUtil::formatForDisplay(strtolower($parts[1]));
            $changeset = (int)DataUtil::formatForDisplay($parts[2]);
            $displayproject = (strtolower($project) == 'core') ? 'Zikula' : $project;
            $result = '<a href="http://code.zikula.org/' . $project . '/changeset/' . $changeset . '" title="' . __f('Click here to see change set #%1$s of the %2$s project', array($changeset, $displayproject)) . '">' . __f('Change set #%1$s (%2$s project)', array($changeset, $displayproject)) . '</a>';
        } else {
            $result = __('No needle ID', $dom);
        }
        return $result;
    }
}
