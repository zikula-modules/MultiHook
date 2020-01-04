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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\MultiHookModule\Entity\Factory\EntityFactory;
use Zikula\MultiHookModule\Form\Type\Field\TranslationType;
use Zikula\MultiHookModule\Entity\EntryEntity;
use Zikula\MultiHookModule\Helper\FeatureActivationHelper;
use Zikula\MultiHookModule\Helper\ListEntriesHelper;
use Zikula\MultiHookModule\Helper\TranslatableHelper;
use Zikula\MultiHookModule\Traits\ModerationFormFieldsTrait;

/**
 * Entry editing form type base class.
 */
abstract class AbstractEntryType extends AbstractType
{
    use TranslatorTrait;
    use ModerationFormFieldsTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        VariableApiInterface $variableApi,
        TranslatableHelper $translatableHelper,
        ListEntriesHelper $listHelper,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->variableApi = $variableApi;
        $this->translatableHelper = $translatableHelper;
        $this->listHelper = $listHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addEntityFields($builder, $options);
        $this->addModerationFields($builder, $options);
        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds basic entity fields.
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('longForm', TextType::class, [
            'label' => $this->__('Long form') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The URL, in the case of a link; ignored for censored words.')
            ],
            'help' => $this->__('The URL, in the case of a link; ignored for censored words.'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the long form of the entry.')
            ],
            'required' => false,
        ]);
        
        $builder->add('title', TextType::class, [
            'label' => $this->__('Title') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Only necessary for a link; ignored for censored words.')
            ],
            'help' => $this->__('Only necessary for a link; ignored for censored words.'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the title of the entry.')
            ],
            'required' => false,
        ]);
        
        if ($this->variableApi->getSystemVar('multilingual') && $this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, 'entry')) {
            $supportedLanguages = $this->translatableHelper->getSupportedLanguages('entry');
            if (is_array($supportedLanguages) && count($supportedLanguages) > 1) {
                $currentLanguage = $this->translatableHelper->getCurrentLanguage();
                $translatableFields = $this->translatableHelper->getTranslatableFields('entry');
                $mandatoryFields = $this->translatableHelper->getMandatoryFields('entry');
                foreach ($supportedLanguages as $language) {
                    if ($language === $currentLanguage) {
                        continue;
                    }
                    $builder->add('translations' . $language, TranslationType::class, [
                        'fields' => $translatableFields,
                        'mandatory_fields' => $mandatoryFields[$language],
                        'values' => $options['translations'][$language] ?? []
                    ]);
                }
            }
        }
        
        $builder->add('shortForm', TextType::class, [
            'label' => $this->__('Short form') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 100,
                'class' => '',
                'title' => $this->__('Enter the short form of the entry.')
            ],
            'required' => true,
        ]);
        
        $listEntries = $this->listHelper->getEntries('entry', 'entryType');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('entryType', ChoiceType::class, [
            'label' => $this->__('Entry type') . ':',
            'empty_data' => '',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the entry type.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        
        $builder->add('active', CheckboxType::class, [
            'label' => $this->__('Active') . ':',
            'label_attr' => [
                'class' => 'switch-custom'
            ],
            'attr' => [
                'class' => '',
                'title' => $this->__('active ?')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds submit buttons.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = []): void
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], SubmitType::class, [
                'label' => $action['title'],
                'icon' => 'delete' === $action['id'] ? 'fa-trash-alt' : '',
                'attr' => [
                    'class' => $action['buttonClass']
                ]
            ]);
            if ('create' === $options['mode'] && 'submit' === $action['id']) {
                // add additional button to submit item and return to create form
                $builder->add('submitrepeat', SubmitType::class, [
                    'label' => $this->__('Submit and repeat'),
                    'icon' => 'fa-repeat',
                    'attr' => [
                        'class' => $action['buttonClass']
                    ]
                ]);
            }
        }
        $builder->add('reset', ResetType::class, [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulamultihookmodule_entry';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => EntryEntity::class,
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createEntry();
                },
                'error_mapping' => [
                ],
                'mode' => 'create',
                'actions' => [],
                'has_moderate_permission' => false,
                'allow_moderation_specific_creator' => false,
                'allow_moderation_specific_creation_date' => false,
                'translations' => [],
            ])
            ->setRequired(['mode', 'actions'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('actions', 'array')
            ->setAllowedTypes('has_moderate_permission', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creator', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creation_date', 'bool')
            ->setAllowedTypes('translations', 'array')
            ->setAllowedValues('mode', ['create', 'edit'])
        ;
    }
}
