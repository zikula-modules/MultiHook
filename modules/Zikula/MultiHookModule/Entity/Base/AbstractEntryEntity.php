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
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\MultiHookModule\Traits\StandardFieldsTrait;
use Zikula\MultiHookModule\Validator\Constraints as MultiHookAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for entry entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractEntryEntity extends EntityAccess implements Translatable
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'entry';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=1000000000)
     * @var int $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @MultiHookAssert\ListEntry(entityName="entry", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="100")
     * @var string $shortForm
     */
    protected $shortForm = '';
    
    /**
     * The URL, in the case of a link; ignored for censored words.
     *
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $longForm
     */
    protected $longForm = '';
    
    /**
     * Only necessary for a link; ignored for censored words.
     *
     * @Gedmo\Translatable
     * @ORM\Column(length=255)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $title
     */
    protected $title = '';
    
    /**
     * @ORM\Column(length=10)
     * @Assert\NotBlank()
     * @MultiHookAssert\ListEntry(entityName="entry", propertyName="entryType", multiple=false)
     * @var string $entryType
     */
    protected $entryType = '';
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var bool $active
     */
    protected $active = true;
    
    
    /**
     * Used locale to override Translation listener's locale.
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\Locale()
     * @Gedmo\Locale
     * @var string $locale
     */
    protected $locale;
    
    
    /**
     * EntryEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
    }
    
    public function get_objectType(): string
    {
        return $this->_objectType;
    }
    
    public function set_objectType(string $_objectType): void
    {
        if ($this->_objectType !== $_objectType) {
            $this->_objectType = $_objectType ?? '';
        }
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id = null): void
    {
        if ((int)$this->id !== $id) {
            $this->id = $id;
        }
    }
    
    public function getWorkflowState(): string
    {
        return $this->workflowState;
    }
    
    public function setWorkflowState(string $workflowState): void
    {
        if ($this->workflowState !== $workflowState) {
            $this->workflowState = $workflowState ?? '';
        }
    }
    
    public function getShortForm(): string
    {
        return $this->shortForm;
    }
    
    public function setShortForm(string $shortForm): void
    {
        if ($this->shortForm !== $shortForm) {
            $this->shortForm = $shortForm ?? '';
        }
    }
    
    public function getLongForm(): string
    {
        return $this->longForm;
    }
    
    public function setLongForm(string $longForm): void
    {
        if ($this->longForm !== $longForm) {
            $this->longForm = $longForm ?? '';
        }
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function setTitle(string $title): void
    {
        if ($this->title !== $title) {
            $this->title = $title ?? '';
        }
    }
    
    public function getEntryType(): string
    {
        return $this->entryType;
    }
    
    public function setEntryType(string $entryType): void
    {
        if ($this->entryType !== $entryType) {
            $this->entryType = $entryType ?? '';
        }
    }
    
    public function getActive(): bool
    {
        return $this->active;
    }
    
    public function setActive(bool $active): void
    {
        if ((bool)$this->active !== $active) {
            $this->active = $active;
        }
    }
    
    public function getLocale()
    {
        return $this->locale;
    }
    
    public function setLocale($locale = null): void
    {
        if ($this->locale !== $locale) {
            $this->locale = $locale;
        }
    }
    
    /**
     * Creates url arguments array for easy creation of display urls.
     */
    public function createUrlArgs(): array
    {
        return [
            'id' => $this->getId()
        ];
    }
    
    /**
     * Returns the primary key.
     */
    public function getKey(): ?int
    {
        return $this->getId();
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     */
    public function supportsHookSubscribers(): bool
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     */
    public function getHookAreaPrefix(): string
    {
        return 'zikulamultihookmodule.ui_hooks.entries';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     */
    public function getRelatedObjectsToPersist(array &$objects = []): array
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     */
    public function __toString(): string
    {
        return 'Entry ' . $this->getKey() . ': ' . $this->getShortForm();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
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
    
        // otherwise proceed
    
        // unset identifier
        $this->setId(0);
    
        // reset workflow
        $this->setWorkflowState('initial');
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    
    }
}
