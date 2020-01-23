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

namespace Zikula\MultiHookModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Bundle\CoreBundle\Event\GenericEvent;
use Zikula\UsersModule\UserEvents;

/**
 * Event handler base class for events of the Users module.
 */
abstract class AbstractUsersListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::CONFIG_UPDATED => ['configUpdated', 5]
        ];
    }
    
    /**
     * Listener for the `module.users.config.updated` event.
     *
     * Occurs after the Users module configuration has been
     * updated via the administration interface.
     *
     * Event data is populated by the new values.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function configUpdated(GenericEvent $event): void
    {
    }
}
