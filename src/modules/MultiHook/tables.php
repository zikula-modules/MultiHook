<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: tables.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

function MultiHook_tables()
{
    // Initialise table array
    $tables = array();

    $tables['multihook'] = 'multihook';
    $tables['multihook_column'] = array('aid'      => 'aid',
                                        'short'    => 'short',
                                        'tlong'    => 'tlong',
                                        'title'    => 'title',
                                        'type'     => 'type',
                                        'language' => 'language');

    // column definitions
    $tables['multihook_column_def'] = array('aid'      => "I AUTO PRIMARY",
                                            'short'    => "C(100) NOTNULL DEFAULT ''",
                                            'tlong'    => "C(200) NOTNULL DEFAULT ''",
                                            'title'    => "C(100) NOTNULL DEFAULT ''",
                                            'type'     => "I1 NOTNULL DEFAULT 0",
                                            'language' => "C(100) NOTNULL DEFAULT ''");

    // addtitional indexes
    $tables['multihook_column_idx'] = array ('short' => 'short',
                                             'type'  => 'type');

    // Return the table information
    return $tables;
}
