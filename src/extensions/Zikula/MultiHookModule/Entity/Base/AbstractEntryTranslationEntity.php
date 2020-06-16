<?php

/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\MultiHookModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * Entity extension domain class storing entry translations.
 *
 * This is the base translation class for entry entities.
 */
abstract class AbstractEntryTranslationEntity extends AbstractTranslation
{
    
    /**
     * Use a length of 140 instead of 255 to avoid too long keys for the indexes.
     *
     * @var string $objectClass
     *
     * @ORM\Column(name="object_class", type="string", length=140)
     */
    protected $objectClass;
    
    /**
     * Use integer instead of string for increased performance.
     * @see https://github.com/Atlantic18/DoctrineExtensions/issues/1512
     *
     * @var integer $foreignKey
     *
     * @ORM\Column(name="foreign_key", type="integer")
     */
    protected $foreignKey;
    
    /**
     * Clone interceptor implementation.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }
    
        // unset identifier
        $this->id = 0;
    }
}