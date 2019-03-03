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

namespace Zikula\MultiHookModule\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass for collecting needles.
 */
class NeedleCollectorPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('zikula_multihook_module.collector.needle_collector')) {
            return;
        }

        $collectorDefinition = $container->getDefinition('zikula_multihook_module.collector.needle_collector');

        $taggedServices = $container->findTaggedServiceIds('zikula.multihook_needle');
        foreach ($taggedServices as $id => $tagParameters) {
            $collectorDefinition->addMethodCall('add', [new Reference($id)]);
        }
    }
}