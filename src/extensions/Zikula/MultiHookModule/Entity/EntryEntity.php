<?php

/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\MultiHookModule\Entity;

use Zikula\MultiHookModule\Entity\Base\AbstractEntryEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for entry entities.
 * @Gedmo\TranslationEntity(class="Zikula\MultiHookModule\Entity\EntryTranslationEntity")
 * @ORM\Entity(repositoryClass="Zikula\MultiHookModule\Entity\Repository\EntryRepository")
 * @ORM\Table(name="zikula_multih_entry",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class EntryEntity extends BaseEntity
{
    // feel free to add your own methods here
}
