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

namespace Zikula\MultiHookModule\Twig;

use Twig_Extension;
use Zikula\MultiHookModule\Collector\EntryProviderCollector;
use Zikula\MultiHookModule\Collector\NeedleCollector;

/**
 * Twig extension implementation class.
 */
class CustomTwigExtension extends Twig_Extension
{
    /**
     * @var EntryProviderCollector
     */
    protected $entryProviderCollector;

    /**
     * @var NeedleCollector
     */
    protected $needleCollector;

    /**
     * CustomTwigExtension constructor.
     *
     * @param EntryProviderCollector $entryProviderCollector
     * @param NeedleCollector        $needleCollector
     */
    public function __construct(
        EntryProviderCollector $entryProviderCollector,
        NeedleCollector $needleCollector
    ) {
        $this->entryProviderCollector = $entryProviderCollector;
        $this->needleCollector = $needleCollector;
    }

    /**
     * Returns a list of custom Twig functions.
     *
     * @return \Twig_SimpleFunction[] List of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('zikulamultihookmodule_getEntryProviders', [$this, 'getEntryProviders']),
            new \Twig_SimpleFunction('zikulamultihookmodule_getNeedles', [$this, 'getNeedles'])
        ];
    }

    /**
     * The zikulamultihookmodule_getEntryProviders function returns a list of all entry providers.
     * Examples:
     *    {% set entryProviders = zikulamultihookmodule_getEntryProviders() %}     {# only active ones #}
     *    {% set entryProviders = zikulamultihookmodule_getEntryProviders(true) %} {# also inactive ones #}
     *
     * @param boolean $includeInactive Whether also inactive entry providers should be returned or not (default false)
     *
     * @return array
     */
    public function getEntryProviders($includeInactive = false)
    {
        if (true === $includeInactive) {
            return $this->entryProviderCollector->getAll();
        }

        return $this->entryProviderCollector->getActive();
    }

    /**
     * The zikulamultihookmodule_getNeedles function returns a list of all needles.
     * Examples:
     *    {% set needles = zikulamultihookmodule_getNeedles() %}     {# only active ones #}
     *    {% set needles = zikulamultihookmodule_getNeedles(true) %} {# also inactive ones #}
     *
     * @param boolean $includeInactive Whether also inactive needles should be returned or not (default false)
     *
     * @return array
     */
    public function getNeedles($includeInactive = false)
    {
        if (true === $includeInactive) {
            return $this->needleCollector->getAll();
        }

        return $this->needleCollector->getActive();
    }
}
