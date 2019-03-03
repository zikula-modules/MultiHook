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

namespace Zikula\MultiHookModule\EntryProvider;

use Zikula\Common\Translator\TranslatorInterface;
use Zikula\MultiHookModule\Entity\Factory\EntityFactory;

/**
 * Default entry provider.
 */
class DefaultEntryProvider
{
    /**
     * Translator instance
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * Bundle name
     *
     * @var string
     */
    private $bundleName;

    /**
     * Translation domain
     *
     * @var string
     */
    private $domain;

    /**
     * The name of this provider
     *
     * @var string
     */
    private $name;

    /**
     * DefaultEntryProvider constructor.
     *
     * @param TranslatorInterface $translator
     * @param EntityFactory $entityFactory
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory
    ) {
        $this->translator = $translator;
        $this->entityFactory = $entityFactory;

        $nsParts = explode('\\', get_class($this));
        $vendor = $nsParts[0];
        $nameAndType = $nsParts[1];

        $this->bundleName = $vendor . $nameAndType;
        $this->domain = strtolower($this->bundleName);
        $this->name = str_replace('Type', '', array_pop($nsParts));
    }

    /**
     * Returns the bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * Returns the name of this content type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the icon name (FontAwesome icon code suffix, e.g. "pencil").
     *
     * @return string
     */
    public function getIcon()
    {
        return 'cube';
    }

    /**
     * Returns the title of this content type.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->translator->__('Default functionality', $this->domain);
    }

    /**
     * Returns the description of this content type.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->translator->__('Provides MultiHook\'s own entries.', $this->domain);
    }

    /**
     * Returns an extended plugin information shown on settings page.
     *
     * @return string
     */
    public function getAdminInfo()
    {
        return '';
    }

    /**
     * Returns whether this content type is active or not.
     *
     * @return boolean
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Returns entries for given entry types.
     *
     * @param string[] $entryTypes
     * @return array
     */
    public function getEntries(array $entryTypes = [])
    {
        $result = [];

        if (count($entryTypes) > 0) {
            $result = $this->entityFactory->getRepository('entry')
                ->selectWhere('tbl.active = 1 AND tbl.entryType IN (\'' . implode('\', \'', $entryTypes) . '\')');
        }

        return $result;
    }
}