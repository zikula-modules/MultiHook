<?php
/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultiHookModule\Helper\Base;

use Zikula\MultiHookModule\Entity\Factory\EntityFactory;

/**
 * Helper base class for model layer methods.
 */
abstract class AbstractModelHelper
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }
    
    /**
     * Determines whether creating an instance of a certain object type is possible.
     * This is when
     *     - it has no incoming bidirectional non-nullable relationships.
     *     - the edit type of all those relationships has PASSIVE_EDIT and auto completion is used on the target side
     *       (then a new source object can be created while creating the target object).
     *     - corresponding source objects exist already in the system.
     *
     * Note that even creation of a certain object is possible, it may still be forbidden for the current user
     * if he does not have the required permission level.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return bool Whether a new instance can be created or not
     */
    public function canBeCreated($objectType = '')
    {
        $result = false;
    
        switch ($objectType) {
            case 'entry':
                $result = true;
                break;
        }
    
        return $result;
    }
    
    /**
     * Determines whether there exists at least one instance of a certain object type in the database.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return bool Whether at least one instance exists or not
     */
    protected function hasExistingInstances($objectType = '')
    {
        $repository = $this->entityFactory->getRepository($objectType);
        if (null === $repository) {
            return false;
        }
    
        return 0 < $repository->selectCount();
    }
    
    /**
     * Returns a desired sorting criteria for passing it to a repository method.
     *
     * @param string $objectType Name of treated entity type
     * @param string $sorting The type of sorting (newest, random, default)
     *
     * @return string The order by clause
     */
    public function resolveSortParameter($objectType = '', $sorting = 'default')
    {
        if ('random' === $sorting) {
            return 'RAND()';
        }
    
        $hasStandardFields = in_array($objectType, ['entry']);
    
        $sortParam = '';
        if ('newest' === $sorting) {
            if (true === $hasStandardFields) {
                $sortParam = 'createdDate DESC';
            } else {
                $sortParam = $this->entityFactory->getIdField($objectType) . ' DESC';
            }
        } elseif ('updated' === $sorting) {
            if (true === $hasStandardFields) {
                $sortParam = 'updatedDate DESC';
            } else {
                $sortParam = $this->entityFactory->getIdField($objectType) . ' DESC';
            }
        } elseif ('default' === $sorting) {
            $repository = $this->entityFactory->getRepository($objectType);
            $sortParam = $repository->getDefaultSortingField();
        }
    
        return $sortParam;
    }
}
