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

namespace Zikula\MultiHookModule\Base;

use RuntimeException;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula\MultiHookModule\Entity\EntryEntity;
use Zikula\MultiHookModule\Entity\EntryTranslationEntity;

/**
 * Installer base class.
 */
abstract class AbstractMultiHookModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var array
     */
    protected $entities = [
        EntryEntity::class,
        EntryTranslationEntity::class,
    ];

    /**
     * Install the ZikulaMultiHookModule application.
     *
     * @return boolean True on success, or false
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        $logger = $this->container->get('logger');
    
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->entities);
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'ZikulaMultiHookModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('showEditLink', true);
        $this->setVar('replaceOnlyFirstInstanceOfItems', false);
        $this->setVar('applyReplacementsToCodeTags', false);
        $this->setVar('replaceAbbreviations', true);
        $this->setVar('replaceAcronyms', true);
        $this->setVar('replaceAbbreviationsWithLongText', false);
        $this->setVar('replaceLinks', true);
        $this->setVar('replaceLinksWithTitle', false);
        $this->setVar('cssClassForExternalLinks', '');
        $this->setVar('replaceCensoredWords', true);
        $this->setVar('replaceCensoredWordsWhenTheyArePartOfOtherWords', false);
        $this->setVar('doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars', false);
        $this->setVar('replaceNeedles', true);
        $this->setVar('entryEntriesPerPage', 10);
        $this->setVar('showOnlyOwnEntries', false);
        $this->setVar('allowModerationSpecificCreatorForEntry', false);
        $this->setVar('allowModerationSpecificCreationDateForEntry', false);
    
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the ZikulaMultiHookModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->entities);
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'ZikulaMultiHookModule', 'errorMessage' => $exception->getMessage()]);
    
                    return false;
                }
        }
    */
    
        // update successful
        return true;
    }
    
    /**
     * Uninstall ZikulaMultiHookModule.
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        $logger = $this->container->get('logger');
    
        try {
            $this->schemaTool->drop($this->entities);
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'ZikulaMultiHookModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // remove all module vars
        $this->delVars();
    
        // uninstallation successful
        return true;
    }
}
