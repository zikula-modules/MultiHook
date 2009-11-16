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

function MultiHook_pntables()
{
    // Initialise table array
    $pntable = array();


    $multihook = DBUtil::getLimitedTablename('multihook') ;
    $pntable['multihook'] = $multihook;
    $pntable['multihook_column'] = array('aid'      => 'pn_aid',
                                         'short'    => 'pn_short',
                                         'long'     => 'pn_long',
                                         'title'    => 'pn_title',
                                         'type'     => 'pn_type',
                                         'language' => 'pn_language');

    // column definitions
    $pntable['multihook_column_def'] = array('aid'      => "I AUTO PRIMARY",
                                             'short'    => "C(100) NOTNULL DEFAULT ''",
                                             'long'     => "C(200) NOTNULL DEFAULT ''",
                                             'title'    => "C(100) NOTNULL DEFAULT ''",
                                             'type'     => "I1 NOTNULL DEFAULT 0",
                                             'language' => "C(100) NOTNULL DEFAULT ''");

    // addtitional indexes
    $pntable['multihook_column_idx'] = array ('short' => 'short',
                                              'type'  => 'type');

    // Return the table information
    return $pntable;
}
