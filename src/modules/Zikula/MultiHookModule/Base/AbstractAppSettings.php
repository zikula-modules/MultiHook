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

use Symfony\Component\Validator\Constraints as Assert;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Application settings class for handling module variables.
 */
abstract class AbstractAppSettings
{
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $replaceAbbreviationsWithLongText
     */
    protected $replaceAbbreviationsWithLongText = false;
    
    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $replaceLinksWithTitle
     */
    protected $replaceLinksWithTitle = false;
    
    /**
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $cssClassForExternalLinks
     */
    protected $cssClassForExternalLinks = '';
    
    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $applyReplacementsToCodeTags
     */
    protected $applyReplacementsToCodeTags = false;
    
    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $showEditLink
     */
    protected $showEditLink = true;
    
    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $replaceOnlyFirstInstanceOfItems
     */
    protected $replaceOnlyFirstInstanceOfItems = false;
    
    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $replaceCensoredWordsWhenTheyArePartOfOtherWords
     */
    protected $replaceCensoredWordsWhenTheyArePartOfOtherWords = false;
    
    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars
     */
    protected $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars = false;
    
    /**
     * The amount of entries shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $entryEntriesPerPage
     */
    protected $entryEntriesPerPage = 10;
    
    /**
     * Whether only own entries should be shown on view pages by default or not
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $showOnlyOwnEntries
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * Whether to allow moderators choosing a user which will be set as creator.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreatorForEntry
     */
    protected $allowModerationSpecificCreatorForEntry = false;
    
    /**
     * Whether to allow moderators choosing a custom creation date.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $allowModerationSpecificCreationDateForEntry
     */
    protected $allowModerationSpecificCreationDateForEntry = false;
    
    
    /**
     * AppSettings constructor.
     *
     * @param VariableApiInterface $variableApi
     */
    public function __construct(
        VariableApiInterface $variableApi
    ) {
        $this->variableApi = $variableApi;
    
        $this->load();
    }
    
    /**
     * Returns the replace abbreviations with long text.
     *
     * @return boolean
     */
    public function getReplaceAbbreviationsWithLongText()
    {
        return $this->replaceAbbreviationsWithLongText;
    }
    
    /**
     * Sets the replace abbreviations with long text.
     *
     * @param boolean $replaceAbbreviationsWithLongText
     *
     * @return void
     */
    public function setReplaceAbbreviationsWithLongText($replaceAbbreviationsWithLongText)
    {
        if (boolval($this->replaceAbbreviationsWithLongText) !== boolval($replaceAbbreviationsWithLongText)) {
            $this->replaceAbbreviationsWithLongText = boolval($replaceAbbreviationsWithLongText);
        }
    }
    
    /**
     * Returns the replace links with title.
     *
     * @return boolean
     */
    public function getReplaceLinksWithTitle()
    {
        return $this->replaceLinksWithTitle;
    }
    
    /**
     * Sets the replace links with title.
     *
     * @param boolean $replaceLinksWithTitle
     *
     * @return void
     */
    public function setReplaceLinksWithTitle($replaceLinksWithTitle)
    {
        if (boolval($this->replaceLinksWithTitle) !== boolval($replaceLinksWithTitle)) {
            $this->replaceLinksWithTitle = boolval($replaceLinksWithTitle);
        }
    }
    
    /**
     * Returns the css class for external links.
     *
     * @return string
     */
    public function getCssClassForExternalLinks()
    {
        return $this->cssClassForExternalLinks;
    }
    
    /**
     * Sets the css class for external links.
     *
     * @param string $cssClassForExternalLinks
     *
     * @return void
     */
    public function setCssClassForExternalLinks($cssClassForExternalLinks)
    {
        if ($this->cssClassForExternalLinks !== $cssClassForExternalLinks) {
            $this->cssClassForExternalLinks = isset($cssClassForExternalLinks) ? $cssClassForExternalLinks : '';
        }
    }
    
    /**
     * Returns the apply replacements to code tags.
     *
     * @return boolean
     */
    public function getApplyReplacementsToCodeTags()
    {
        return $this->applyReplacementsToCodeTags;
    }
    
    /**
     * Sets the apply replacements to code tags.
     *
     * @param boolean $applyReplacementsToCodeTags
     *
     * @return void
     */
    public function setApplyReplacementsToCodeTags($applyReplacementsToCodeTags)
    {
        if (boolval($this->applyReplacementsToCodeTags) !== boolval($applyReplacementsToCodeTags)) {
            $this->applyReplacementsToCodeTags = boolval($applyReplacementsToCodeTags);
        }
    }
    
    /**
     * Returns the show edit link.
     *
     * @return boolean
     */
    public function getShowEditLink()
    {
        return $this->showEditLink;
    }
    
    /**
     * Sets the show edit link.
     *
     * @param boolean $showEditLink
     *
     * @return void
     */
    public function setShowEditLink($showEditLink)
    {
        if (boolval($this->showEditLink) !== boolval($showEditLink)) {
            $this->showEditLink = boolval($showEditLink);
        }
    }
    
    /**
     * Returns the replace only first instance of items.
     *
     * @return boolean
     */
    public function getReplaceOnlyFirstInstanceOfItems()
    {
        return $this->replaceOnlyFirstInstanceOfItems;
    }
    
    /**
     * Sets the replace only first instance of items.
     *
     * @param boolean $replaceOnlyFirstInstanceOfItems
     *
     * @return void
     */
    public function setReplaceOnlyFirstInstanceOfItems($replaceOnlyFirstInstanceOfItems)
    {
        if (boolval($this->replaceOnlyFirstInstanceOfItems) !== boolval($replaceOnlyFirstInstanceOfItems)) {
            $this->replaceOnlyFirstInstanceOfItems = boolval($replaceOnlyFirstInstanceOfItems);
        }
    }
    
    /**
     * Returns the replace censored words when they are part of other words.
     *
     * @return boolean
     */
    public function getReplaceCensoredWordsWhenTheyArePartOfOtherWords()
    {
        return $this->replaceCensoredWordsWhenTheyArePartOfOtherWords;
    }
    
    /**
     * Sets the replace censored words when they are part of other words.
     *
     * @param boolean $replaceCensoredWordsWhenTheyArePartOfOtherWords
     *
     * @return void
     */
    public function setReplaceCensoredWordsWhenTheyArePartOfOtherWords($replaceCensoredWordsWhenTheyArePartOfOtherWords)
    {
        if (boolval($this->replaceCensoredWordsWhenTheyArePartOfOtherWords) !== boolval($replaceCensoredWordsWhenTheyArePartOfOtherWords)) {
            $this->replaceCensoredWordsWhenTheyArePartOfOtherWords = boolval($replaceCensoredWordsWhenTheyArePartOfOtherWords);
        }
    }
    
    /**
     * Returns the do not censor first and last letter in words with more than two chars.
     *
     * @return boolean
     */
    public function getDoNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars()
    {
        return $this->doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars;
    }
    
    /**
     * Sets the do not censor first and last letter in words with more than two chars.
     *
     * @param boolean $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars
     *
     * @return void
     */
    public function setDoNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars($doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars)
    {
        if (boolval($this->doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars) !== boolval($doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars)) {
            $this->doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars = boolval($doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars);
        }
    }
    
    /**
     * Returns the entry entries per page.
     *
     * @return integer
     */
    public function getEntryEntriesPerPage()
    {
        return $this->entryEntriesPerPage;
    }
    
    /**
     * Sets the entry entries per page.
     *
     * @param integer $entryEntriesPerPage
     *
     * @return void
     */
    public function setEntryEntriesPerPage($entryEntriesPerPage)
    {
        if (intval($this->entryEntriesPerPage) !== intval($entryEntriesPerPage)) {
            $this->entryEntriesPerPage = intval($entryEntriesPerPage);
        }
    }
    
    /**
     * Returns the show only own entries.
     *
     * @return boolean
     */
    public function getShowOnlyOwnEntries()
    {
        return $this->showOnlyOwnEntries;
    }
    
    /**
     * Sets the show only own entries.
     *
     * @param boolean $showOnlyOwnEntries
     *
     * @return void
     */
    public function setShowOnlyOwnEntries($showOnlyOwnEntries)
    {
        if (boolval($this->showOnlyOwnEntries) !== boolval($showOnlyOwnEntries)) {
            $this->showOnlyOwnEntries = boolval($showOnlyOwnEntries);
        }
    }
    
    /**
     * Returns the allow moderation specific creator for entry.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreatorForEntry()
    {
        return $this->allowModerationSpecificCreatorForEntry;
    }
    
    /**
     * Sets the allow moderation specific creator for entry.
     *
     * @param boolean $allowModerationSpecificCreatorForEntry
     *
     * @return void
     */
    public function setAllowModerationSpecificCreatorForEntry($allowModerationSpecificCreatorForEntry)
    {
        if (boolval($this->allowModerationSpecificCreatorForEntry) !== boolval($allowModerationSpecificCreatorForEntry)) {
            $this->allowModerationSpecificCreatorForEntry = boolval($allowModerationSpecificCreatorForEntry);
        }
    }
    
    /**
     * Returns the allow moderation specific creation date for entry.
     *
     * @return boolean
     */
    public function getAllowModerationSpecificCreationDateForEntry()
    {
        return $this->allowModerationSpecificCreationDateForEntry;
    }
    
    /**
     * Sets the allow moderation specific creation date for entry.
     *
     * @param boolean $allowModerationSpecificCreationDateForEntry
     *
     * @return void
     */
    public function setAllowModerationSpecificCreationDateForEntry($allowModerationSpecificCreationDateForEntry)
    {
        if (boolval($this->allowModerationSpecificCreationDateForEntry) !== boolval($allowModerationSpecificCreationDateForEntry)) {
            $this->allowModerationSpecificCreationDateForEntry = boolval($allowModerationSpecificCreationDateForEntry);
        }
    }
    
    
    /**
     * Loads module variables from the database.
     */
    protected function load()
    {
        $moduleVars = $this->variableApi->getAll('ZikulaMultiHookModule');
    
        if (isset($moduleVars['replaceAbbreviationsWithLongText'])) {
            $this->setReplaceAbbreviationsWithLongText($moduleVars['replaceAbbreviationsWithLongText']);
        }
        if (isset($moduleVars['replaceLinksWithTitle'])) {
            $this->setReplaceLinksWithTitle($moduleVars['replaceLinksWithTitle']);
        }
        if (isset($moduleVars['cssClassForExternalLinks'])) {
            $this->setCssClassForExternalLinks($moduleVars['cssClassForExternalLinks']);
        }
        if (isset($moduleVars['applyReplacementsToCodeTags'])) {
            $this->setApplyReplacementsToCodeTags($moduleVars['applyReplacementsToCodeTags']);
        }
        if (isset($moduleVars['showEditLink'])) {
            $this->setShowEditLink($moduleVars['showEditLink']);
        }
        if (isset($moduleVars['replaceOnlyFirstInstanceOfItems'])) {
            $this->setReplaceOnlyFirstInstanceOfItems($moduleVars['replaceOnlyFirstInstanceOfItems']);
        }
        if (isset($moduleVars['replaceCensoredWordsWhenTheyArePartOfOtherWords'])) {
            $this->setReplaceCensoredWordsWhenTheyArePartOfOtherWords($moduleVars['replaceCensoredWordsWhenTheyArePartOfOtherWords']);
        }
        if (isset($moduleVars['doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars'])) {
            $this->setDoNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars($moduleVars['doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars']);
        }
        if (isset($moduleVars['entryEntriesPerPage'])) {
            $this->setEntryEntriesPerPage($moduleVars['entryEntriesPerPage']);
        }
        if (isset($moduleVars['showOnlyOwnEntries'])) {
            $this->setShowOnlyOwnEntries($moduleVars['showOnlyOwnEntries']);
        }
        if (isset($moduleVars['allowModerationSpecificCreatorForEntry'])) {
            $this->setAllowModerationSpecificCreatorForEntry($moduleVars['allowModerationSpecificCreatorForEntry']);
        }
        if (isset($moduleVars['allowModerationSpecificCreationDateForEntry'])) {
            $this->setAllowModerationSpecificCreationDateForEntry($moduleVars['allowModerationSpecificCreationDateForEntry']);
        }
    }
    
    /**
     * Saves module variables into the database.
     */
    public function save()
    {
        $this->variableApi->set('ZikulaMultiHookModule', 'replaceAbbreviationsWithLongText', $this->getReplaceAbbreviationsWithLongText());
        $this->variableApi->set('ZikulaMultiHookModule', 'replaceLinksWithTitle', $this->getReplaceLinksWithTitle());
        $this->variableApi->set('ZikulaMultiHookModule', 'cssClassForExternalLinks', $this->getCssClassForExternalLinks());
        $this->variableApi->set('ZikulaMultiHookModule', 'applyReplacementsToCodeTags', $this->getApplyReplacementsToCodeTags());
        $this->variableApi->set('ZikulaMultiHookModule', 'showEditLink', $this->getShowEditLink());
        $this->variableApi->set('ZikulaMultiHookModule', 'replaceOnlyFirstInstanceOfItems', $this->getReplaceOnlyFirstInstanceOfItems());
        $this->variableApi->set('ZikulaMultiHookModule', 'replaceCensoredWordsWhenTheyArePartOfOtherWords', $this->getReplaceCensoredWordsWhenTheyArePartOfOtherWords());
        $this->variableApi->set('ZikulaMultiHookModule', 'doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars', $this->getDoNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars());
        $this->variableApi->set('ZikulaMultiHookModule', 'entryEntriesPerPage', $this->getEntryEntriesPerPage());
        $this->variableApi->set('ZikulaMultiHookModule', 'showOnlyOwnEntries', $this->getShowOnlyOwnEntries());
        $this->variableApi->set('ZikulaMultiHookModule', 'allowModerationSpecificCreatorForEntry', $this->getAllowModerationSpecificCreatorForEntry());
        $this->variableApi->set('ZikulaMultiHookModule', 'allowModerationSpecificCreationDateForEntry', $this->getAllowModerationSpecificCreationDateForEntry());
    }
}
