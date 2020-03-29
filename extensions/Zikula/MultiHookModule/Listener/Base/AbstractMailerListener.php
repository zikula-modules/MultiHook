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
use Symfony\Component\Mailer\Event\MessageEvent;

/**
 * Event handler base class for mailing events.
 */
abstract class AbstractMailerListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => ['onMessageSend', 5]
        ];
    }
    
    /**
     * Listener for the `MessageEvent` event.
     * Allows the transformation of a Message and the Envelope before the email is sent.
     */
    public function onMessageSend(MessageEvent $event): void
    {
    }
}
