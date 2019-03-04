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

namespace Zikula\MultiHookModule\Collector;

/**
 * Entry provider collector implementation class.
 */
class EntryProviderCollector
{
    /**
     * List of service objects
     * @var array
     */
    private $providers;

    /**
     * EntryProviderCollector constructor.
     */
    public function __construct()
    {
        $this->providers = [];
    }

    /**
     * Adds an entry provider to the collection.
     *
     * @param object $entryProvider
     */
    public function add($entryProvider)
    {
        $id = '';
        if (method_exists($entryProvider, 'getBundleName')) {
            $id .= $entryProvider->getBundleName();
        }
        if (method_exists($entryProvider, 'getName')) {
            $id .= $entryProvider->getName();
        } elseif (method_exists($entryProvider, 'getTitle')) {
            $id .= $entryProvider->getTitle();
        }

        $this->providers[$id] = $entryProvider;
    }

    /**
     * Returns an entry provider from the collection by service.id.
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return isset($this->providers[$id]) ? $this->providers[$id] : null;
    }

    /**
     * Returns all providers in the collection.
     *
     * @return object[]
     */
    public function getAll()
    {
        $this->sortProviders();

        return $this->providers;
    }

    /**
     * Returns all active providers in the collection.
     *
     * @return object[]
     */
    public function getActive()
    {
        return array_filter($this->getAll(), function($item) {
            return method_exists($item, 'isActive') ? $item->isActive() : false;
        });
    }

    /**
     * Sorts available providers by their title.
     */
    private function sortProviders() {
        $providers = $this->providers;
        usort($providers, function ($a, $b) {
            if (!method_exists($a, 'getTitle') || !method_exists($b, 'getTitle')) {
                return 0;
            }

            return strcmp($a->getTitle(), $b->getTitle());
        });
        $this->providers = $providers;
    }
}
