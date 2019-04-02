<?php

declare(strict_types=1);

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

use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\MultiHookModule\Entity\EntryEntity;
use Zikula\MultiHookModule\Helper\ListEntriesHelper;

/**
 * Entity display helper base class.
 */
abstract class AbstractEntityDisplayHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var ListEntriesHelper Helper service for managing list entries
     */
    protected $listEntriesHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listEntriesHelper
    ) {
        $this->translator = $translator;
        $this->listEntriesHelper = $listEntriesHelper;
    }
    
    /**
     * Returns the formatted title for a given entity.
     */
    public function getFormattedTitle(EntityAccess $entity): string
    {
        if ($entity instanceof EntryEntity) {
            return $this->formatEntry($entity);
        }
    
        return '';
    }
    
    /**
     * Returns the formatted title for a given entity.
     */
    protected function formatEntry(EntryEntity $entity): string
    {
        return $this->translator->__f('%shortForm%', [
            '%shortForm%' => $entity->getShortForm()
        ]);
    }
    
    /**
     * Returns name of the field used as title / name for entities of this repository.
     */
    public function getTitleFieldName(string $objectType = ''): string
    {
        if ('entry' === $objectType) {
            return 'shortForm';
        }
    
        return '';
    }
    
    /**
     * Returns name of the field used for describing entities of this repository.
     */
    public function getDescriptionFieldName(string $objectType = ''): string
    {
        if ('entry' === $objectType) {
            return 'longForm';
        }
    
        return '';
    }
    
    /**
     * Returns name of the date(time) field to be used for representing the start
     * of this object. Used for providing meta data to the tag module.
     */
    public function getStartDateFieldName(string $objectType = ''): string
    {
        if ('entry' === $objectType) {
            return 'createdDate';
        }
    
        return '';
    }
}
