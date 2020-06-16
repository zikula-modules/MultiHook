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

namespace Zikula\MultiHookModule\Collector;

use Zikula\Common\MultiHook\NeedleInterface;

/**
 * Needle collector implementation class.
 */
class NeedleCollector
{
    /**
     * List of service objects
     * @var array
     */
    private $needles;

    /**
     * @param NeedleInterface[] $needles
     */
    public function __construct(iterable $needles)
    {
        $this->needles = [];
        foreach ($needles as $needle) {
            $this->add($needle);
        }
    }

    /**
     * Adds a needle to the collection.
     */
    public function add(NeedleInterface $needle): void
    {
        $id = str_replace('\\', '_', get_class($needle));

        $this->needles[$id] = $needle;
    }

    /**
     * Returns a needle from the collection by service.id.
     */
    public function get(string $id): ?NeedleInterface
    {
        return $this->needles[$id] ?? null;
    }

    /**
     * Returns all needles in the collection.
     *
     * @return NeedleInterface[]
     */
    public function getAll(): iterable
    {
        $this->sortNeedles();

        return $this->needles;
    }

    /**
     * Returns all active needles in the collection.
     *
     * @return NeedleInterface[]
     */
    public function getActive(): iterable
    {
        return array_filter($this->getAll(), static function (NeedleInterface $item) {
            return $item->isActive();
        });
    }

    /**
     * Sorts available needles by their title.
     */
    private function sortNeedles(): void
    {
        $needles = $this->needles;
        usort($needles, static function (NeedleInterface $a, NeedleInterface $b) {
            return strcmp($a->getTitle(), $b->getTitle());
        });
        $this->needles = $needles;
    }
}