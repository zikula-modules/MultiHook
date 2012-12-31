<?php
/**
 * Copyright Zikula Foundation 2010 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package ZikulaExamples_ExampleDoctrine
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * User entity class.
 *
 * We use annotations to define the entity mappings to database.
 *
 * @ORM\Entity
 * @ORM\Table(name="multihook",indexes={@ORM\index(name="search_idx", columns={"shortform", "type"})})
 */
class MultiHook_Entity_Abac extends Zikula_EntityAccess
{
    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $aid;

    /**
     * Annotation for this field definition.
     *
     * @ORM\Column(length=100)
     */
    private $shortform;

    /**
     * Annotation for this field definition.
     *
     * @ORM\Column(length=200)
     */
    private $longform;

    /**
     * Annotation for this field definition.
     *
     * @ORM\Column(length=100)
     */
    private $title;

    /**
     * Annotation for this field definition.
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * Annotation for this field definition.
     *
     * @ORM\Column(length=100)
     */
    private $language;
    
    private $edit;
    
    private $delete;


    public function __construct()
    {
    }
    
    public function setAbac($shortform, $longform, $title, $type, $language)
    {
        $this->shortform = $shortform;
        $this->longform = $longform;
        $this->title = $title;
        $this->type = $type;
        $this->language = $language;
        $this->setPermissions();
    }
/*
    public function getAbac()
    {
        $abac= array('aid'       => $this->aid,
                     'longform'  => $this->longform,
                     'shortform' => $this->shortform,
                     'title'     => $this->title,
                     'type'      => $this->type,
                     'language'  => $this->language);
        return $abac;
    }
*/        
    public function setPermissions()
    {
        // set permission flags
        $this->edit = false;
        $this->delete = false;
    
        if (SecurityUtil::checkPermission('MultiHook::', $this->shortform.'::'.$this->aid, ACCESS_EDIT)) {
            $this->edit = true;
            if (SecurityUtil::checkPermission('MultiHook::', $this->shortform.'::'.$this->aid, ACCESS_DELETE)) {
                $this->delete = true;
            }
        } 
    }
    
    public function getEdit()
    {
        return $this->edit;
    }

    public function setEdit()
    {
        $this->setEdit = false;
        if (SecurityUtil::checkPermission('MultiHook::', $this->shortform.'::'.$this->aid, ACCESS_EDIT)) {
            $this->edit = true;
        } 
    }

    public function getDelete()
    {
        return $this->delete;
    }

    public function setDelete()
    {
        $this->setDelete = false;
        if ($this->getEdit() == true) {
            if (SecurityUtil::checkPermission('MultiHook::', $this->shortform.'::'.$this->aid, ACCESS_DELETE)) {
                $this->delete = true;
            }
        } 
    }

    public function setLongform($longform)
    {
        $this->longform = $longform;
    }

    public function getLongform()
    {
        return $this->longform;
    }

    public function setShortform($shortform)
    {
        $this->shortform = $shortform;
    }

    public function getShortform()
    {
        return $this->shortform;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function getAid()
    {
        return $this->aid;
    }

    public function remove()
    {
    }

}
