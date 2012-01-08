<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: User.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

abstract class Multihook_NeedleApi
{
    protected $name;

    public function getName()
    {
        return $this->name;
    }

    abstract public function filter($input);
}
