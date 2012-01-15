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

class MultiHook_Needles_Github extends Zikula_AbstractHelper
{
    /**
     * wave needle
     * @param $args['nid'] needle id
     * @return array()
     */

    public function info()
    {
        $info = array('info'          => 'GITHUB-{projectname-{Iissueid|Ccommitid}}',   // possible needles
                      'description'   => $this->__('Link to a issue or commit on github.org'),
                      'casesensitive' => true,
                      'needle'        => 'GITHUB',
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
        $result = '';
        return $result;
    }
}
