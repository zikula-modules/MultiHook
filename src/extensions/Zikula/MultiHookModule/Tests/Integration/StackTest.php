<?php

/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\MultiHookModule\Tests\Integration;

use PHPUnit\Framework\TestCase;

class StackTest extends TestCase
{
    public function testEmpty(): array
    {
        $stack = [];
        self::assertEmpty($stack);

        return $stack;
    }

    /**
     * @depends testEmpty
     */
    public function testPush(array $stack): array
    {
        $stack[] = 'foo';
        self::assertEquals('foo', $stack[count($stack) - 1]);
        self::assertNotEmpty($stack);

        return $stack;
    }

    /**
     * @depends testPush
     */
    public function testPop(array $stack): void
    {
        self::assertEquals('foo', array_pop($stack));
        self::assertEmpty($stack);
    }
}
