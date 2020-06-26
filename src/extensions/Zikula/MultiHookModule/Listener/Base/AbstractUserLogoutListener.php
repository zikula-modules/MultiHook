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

namespace Zikula\MultiHookModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\UsersModule\Event\UserPostLogoutSuccessEvent;

/**
 * Event handler base class for user logout events.
 */
abstract class AbstractUserLogoutListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            UserPostLogoutSuccessEvent::class => ['succeeded', 5]
        ];
    }
    
    /**
     * Listener for the `UserPostLogoutSuccessEvent`.
     *
     * Occurs right after a successful logout.
     */
    public function succeeded(UserPostLogoutSuccessEvent $event): void
    {
    }
}
