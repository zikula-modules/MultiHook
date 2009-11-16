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
 * ticket needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_ticket($args)
{
    $dom = ZLanguage::getModuleDomain('MultiHook');
    // simple replacement, no need to cache anything
    if (isset($args['nid']) && !empty($args['nid'])) {
        if (substr($args['nid'], 0, 1) != '-') {
            $args['nid'] =  '-' . $args['nid'];
        }
        $parts = explode('-', $args['nid']);
        $project = DataUtil::formatForDisplay(strtolower($parts[1]));
        $ticket = (int)DataUtil::formatForDisplay($parts[2]);
        $displayproject = (strtolower($project) == 'core') ? 'Zikula' : $project;
        $result = '<a href="http://code.zikula.org/' . $project . '/ticket/' . $ticket . '" title="' . __f('click here to see ticket #%1%s of the %2$s-project', array($ticket, $displayproject )) . '">' . __f('Ticket #%1$s (%2$s-project)', array($ticket, $displayproject )) . '</a>';
    } else {
        $result = __('no needle id', $dom);
    }
    return $result;
}
