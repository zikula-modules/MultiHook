<?php
/**
 * PostNuke Application Framework
 *
 * @copyright (c) 2004, PostNuke Development Team
 * @link http://www.postnuke.com
 * @version $Id: function.pnformmhlangdropdown.php 20923 2007-01-02 18:33:11Z landseer $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author Jrn Wildt, Frank Schummertz
 * @package PostNuke_Template_Plugins
 * @subpackage Functions
 */

// FIXME: Missing autopostback

/**
 * pnFormDropdownList
 * @package PostNuke_System_Modules
 * @subpackage pnRender
 */
class pnFormMHLangDropDown extends pnFormPlugin
{
    var $inputName;
    var $selectedValue;
    var $dataField;

    var $readOnly;
    var $cssClass;

    var $isValid;
    var $mandatory;
    var $errorMessage;
    var $myLabel;

    var $items = array();


    function getFilename()
    {
        return __FILE__; // FIXME: should be found in smarty's data???
    }


    function create(&$render, $params)
    {
        $this->inputName = $this->id;
        $this->selectedValue = (array_key_exists('selectedValue', $params) ? $params['selectedValue'] : 1);

        $this->readOnly = (array_key_exists('readOnly', $params) ? $params['readOnly'] : false);
        $this->dataField = (array_key_exists('dataField', $params) ? $params['dataField'] : $this->id);

        $this->isValid = true;
        $this->mandatory = (array_key_exists('mandatory', $params) ? $params['mandatory'] : false);
    
        // get the list of languages
        $languagelist = languagelist();
        $this->items['All'] = DataUtil::formatForDisplay(_ALL);
        foreach ($languagelist as $value => $text) {
            $text = DataUtil::formatForDisplay($text);
            $osvalue = DataUtil::formatForOS($value);
            $value = DataUtil::formatForDisplay($value);
            
            if (is_dir("language/{$osvalue}")) {
                $this->items[$value] =  $text;
            }
        }
    }


    function load(&$render, &$params)
    {
    }


    function initialize(&$render)
    {
    }


    function render(&$render)
    {
        $idHtml = $this->getIdHtml();

        $nameHtml = " name=\"{$this->inputName}\"";

        $readOnlyHtml = ($this->readOnly ? " readonly=\"readonly\"" : '');

        $class = '';
        if (!$this->isValid) {
            $class .= ' error';
        }
        if ($this->readOnly) {
            $class .= ' readonly';
        }
        if ($this->cssClass != null) {
            $class .= ' ' .$this->cssClass;
        }

        $classHtml = ($class == '' ? '' : " class=\"$class\"");

        $result = "<select{$idHtml}{$nameHtml}{$readOnlyHtml}{$classHtml}>\n";
        foreach ($this->items as $value => $text) {
            $selected = ($value == $this->selectedValue) ? ' selected="selected"' : '';
            $result .= "<option value=\"$value\"{$selected}>$text</option>\n";
        }
        $result .= "</select>\n";

        return $result;
    }


    function decode(&$render)
    {
        $selvalue = FormUtil::getPassedValue($this->inputName, 0, 'POST');
        if(array_key_exists($selvalue, $this->items)) {
            $this->selectedValue = $selvalue;
        }
    }


    function setError($msg)
    {
        $this->isValid = false;
        $this->errorMessage = $msg;
    }


    function clearValidation(&$render)
    {
        $this->isValid = true;
        $this->errorMessage = null;
    }


    function saveValue(&$render, &$data)
    {
        $data[$this->dataField] = $this->selectedValue;
    }
}




function smarty_function_pnformmhlangdropdown($params, &$render)
{
    return $render->pnFormRegisterPlugin('pnFormMHLangDropdown', $params);
}

?>