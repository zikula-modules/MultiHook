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

namespace Zikula\MultiHookModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\MultiHookModule\AppSettings;

/**
 * Configuration form type base class.
 */
abstract class AbstractConfigType extends AbstractType
{
    use TranslatorTrait;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addGeneralSettingsFields($builder, $options);
        $this->addAbbreviationsAndAcronymsFields($builder, $options);
        $this->addAutomaticLinksFields($builder, $options);
        $this->addCensorFields($builder, $options);
        $this->addNeedlesFields($builder, $options);
        $this->addListViewsFields($builder, $options);
        $this->addModerationFields($builder, $options);

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds fields for general settings fields.
     */
    public function addGeneralSettingsFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('showEditLink', CheckboxType::class, [
            'label' => $this->trans('Show edit link') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The show edit link option')
            ],
            'required' => false,
        ]);
        
        $builder->add('replaceOnlyFirstInstanceOfItems', CheckboxType::class, [
            'label' => $this->trans('Replace only first instance of items') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace only first instance of items option')
            ],
            'required' => false,
        ]);
        
        $builder->add('applyReplacementsToCodeTags', CheckboxType::class, [
            'label' => $this->trans('Apply replacements to code tags') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The apply replacements to code tags option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for abbreviations and acronyms fields.
     */
    public function addAbbreviationsAndAcronymsFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('replaceAbbreviations', CheckboxType::class, [
            'label' => $this->trans('Replace abbreviations') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace abbreviations option')
            ],
            'required' => false,
        ]);
        
        $builder->add('replaceAcronyms', CheckboxType::class, [
            'label' => $this->trans('Replace acronyms') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace acronyms option')
            ],
            'required' => false,
        ]);
        
        $builder->add('replaceAbbreviationsWithLongText', CheckboxType::class, [
            'label' => $this->trans('Replace abbreviations with long text') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace abbreviations with long text option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for automatic links fields.
     */
    public function addAutomaticLinksFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('replaceLinks', CheckboxType::class, [
            'label' => $this->trans('Replace links') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace links option')
            ],
            'required' => false,
        ]);
        
        $builder->add('replaceLinksWithTitle', CheckboxType::class, [
            'label' => $this->trans('Replace links with title') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace links with title option')
            ],
            'required' => false,
        ]);
        
        $builder->add('cssClassForExternalLinks', TextType::class, [
            'label' => $this->trans('Css class for external links') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->trans('Enter the css class for external links.')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for censor fields.
     */
    public function addCensorFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('replaceCensoredWords', CheckboxType::class, [
            'label' => $this->trans('Replace censored words') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace censored words option')
            ],
            'required' => false,
        ]);
        
        $builder->add('replaceCensoredWordsWhenTheyArePartOfOtherWords', CheckboxType::class, [
            'label' => $this->trans('Replace censored words when they are part of other words') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace censored words when they are part of other words option')
            ],
            'required' => false,
        ]);
        
        $builder->add('doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars', CheckboxType::class, [
            'label' => $this->trans('Do not censor first and last letter in words with more than two chars') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The do not censor first and last letter in words with more than two chars option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for needles fields.
     */
    public function addNeedlesFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('replaceNeedles', CheckboxType::class, [
            'label' => $this->trans('Replace needles') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->trans('The replace needles option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for list views fields.
     */
    public function addListViewsFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('entryEntriesPerPage', IntegerType::class, [
            'label' => $this->trans('Entry entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->trans('The amount of entries shown per page')
            ],
            'help' => $this->trans('The amount of entries shown per page'),
            'empty_data' => 10,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->trans('Enter the entry entries per page.') . ' ' . $this->trans('Only digits are allowed.')
            ],
            'required' => true,
        ]);
        
        $builder->add('showOnlyOwnEntries', CheckboxType::class, [
            'label' => $this->trans('Show only own entries') . ':',
            'label_attr' => [
                'class' => 'tooltips switch-custom',
                'title' => $this->trans('Whether only own entries should be shown on view pages by default or not')
            ],
            'help' => $this->trans('Whether only own entries should be shown on view pages by default or not'),
            'attr' => [
                'class' => '',
                'title' => $this->trans('The show only own entries option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for moderation fields.
     */
    public function addModerationFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('allowModerationSpecificCreatorForEntry', CheckboxType::class, [
            'label' => $this->trans('Allow moderation specific creator for entry') . ':',
            'label_attr' => [
                'class' => 'tooltips switch-custom',
                'title' => $this->trans('Whether to allow moderators choosing a user which will be set as creator.')
            ],
            'help' => $this->trans('Whether to allow moderators choosing a user which will be set as creator.'),
            'attr' => [
                'class' => '',
                'title' => $this->trans('The allow moderation specific creator for entry option')
            ],
            'required' => false,
        ]);
        
        $builder->add('allowModerationSpecificCreationDateForEntry', CheckboxType::class, [
            'label' => $this->trans('Allow moderation specific creation date for entry') . ':',
            'label_attr' => [
                'class' => 'tooltips switch-custom',
                'title' => $this->trans('Whether to allow moderators choosing a custom creation date.')
            ],
            'help' => $this->trans('Whether to allow moderators choosing a custom creation date.'),
            'attr' => [
                'class' => '',
                'title' => $this->trans('The allow moderation specific creation date for entry option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds submit buttons.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('save', SubmitType::class, [
            'label' => $this->trans('Update configuration'),
            'icon' => 'fa-check',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);
        $builder->add('reset', ResetType::class, [
            'label' => $this->trans('Reset'),
            'icon' => 'fa-sync',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->trans('Cancel'),
            'validate' => false,
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default'
            ]
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulamultihookmodule_config';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data
                'data_class' => AppSettings::class,
            ]);
    }
}
