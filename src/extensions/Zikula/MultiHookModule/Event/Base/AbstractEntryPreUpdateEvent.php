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

namespace Zikula\MultiHookModule\Event\Base;

use Zikula\MultiHookModule\Entity\EntryEntity;

/**
 * Event base class for filtering entry processing.
 */
abstract class AbstractEntryPreUpdateEvent
{
    /**
     * @var EntryEntity Reference to treated entity instance.
     */
    protected $entry;

    /**
     * @var array Entity change set for preUpdate events.
     */
    protected $entityChangeSet = [];

    public function __construct(EntryEntity $entry, array $entityChangeSet = [])
    {
        $this->entry = $entry;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * @return EntryEntity
     */
    public function getEntry(): EntryEntity
    {
        return $this->entry;
    }

    /**
     * @return array Entity change set
     */
    public function getEntityChangeSet(): array
    {
        return $this->entityChangeSet;
    }

    /**
     * @param array $changeSet Entity change set
     */
    public function setEntityChangeSet(array $changeSet = []): void
    {
        $this->entityChangeSet = $changeSet;
    }
}
