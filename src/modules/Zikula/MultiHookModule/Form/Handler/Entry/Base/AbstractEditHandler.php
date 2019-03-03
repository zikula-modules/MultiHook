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

namespace Zikula\MultiHookModule\Form\Handler\Entry\Base;

use Zikula\MultiHookModule\Form\Handler\Common\EditHandler;
use Zikula\MultiHookModule\Form\Type\EntryType;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use RuntimeException;
use Zikula\MultiHookModule\Helper\FeatureActivationHelper;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the entry object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    /**
     * @inheritDoc
     */
    public function processForm(array $templateParameters = [])
    {
        $this->objectType = 'entry';
        $this->objectTypeCapital = 'Entry';
        $this->objectTypeLower = 'entry';
        
        $this->hasPageLockSupport = true;
        $this->hasTranslatableFields = true;
    
        $result = parent::processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
    
        if ('create' == $this->templateParameters['mode']) {
            if (!$this->modelHelper->canBeCreated($this->objectType)) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', $this->__('Sorry, but you can not create the entry yet as other items are required which must be created before!'));
                $logArgs = ['app' => 'ZikulaMultiHookModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $this->objectType];
                $this->logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', $logArgs);
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
            }
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (for additions like standard fields)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
        $this->templateParameters['supportsHookSubscribers'] = $this->entityRef->supportsHookSubscribers();
    
        return $result;
    }
    
    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        return $this->formFactory->create(EntryType::class, $this->entityRef, $this->getFormOptions());
    }
    
    /**
     * @inheritDoc
     */
    protected function getFormOptions()
    {
        $options = [
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'has_moderate_permission' => $this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADMIN),
            'allow_moderation_specific_creator' => $this->variableApi->get('ZikulaMultiHookModule', 'allowModerationSpecificCreatorFor' . $this->objectTypeCapital, false),
            'allow_moderation_specific_creation_date' => $this->variableApi->get('ZikulaMultiHookModule', 'allowModerationSpecificCreationDateFor' . $this->objectTypeCapital, false),
        ];
    
        $options['translations'] = [];
        foreach ($this->templateParameters['supportedLanguages'] as $language) {
            $options['translations'][$language] = isset($this->templateParameters[$this->objectTypeLower . $language]) ? $this->templateParameters[$this->objectTypeLower . $language] : [];
        }
    
        return $options;
    }

    /**
     * @inheritDoc
     */
    protected function getRedirectCodes()
    {
        $codes = parent::getRedirectCodes();
    
        // user index page of entry area
        $codes[] = 'userIndex';
        // admin index page of entry area
        $codes[] = 'adminIndex';
        // user list of entries
        $codes[] = 'userView';
        // admin list of entries
        $codes[] = 'adminView';
        // user list of own entries
        $codes[] = 'userOwnView';
        // admin list of own entries
        $codes[] = 'adminOwnView';
    
    
        return $codes;
    }

    /**
     * Get the default redirect url. Required if no returnTo parameter has been supplied.
     * This method is called in handleCommand so we know which command has been performed.
     *
     * @param array $args List of arguments
     *
     * @return string The default redirect url
     */
    protected function getDefaultReturnUrl(array $args = [])
    {
        $objectIsPersisted = $args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel');
        if (null !== $this->returnTo && $objectIsPersisted) {
            // return to referer
            return $this->returnTo;
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
        $routePrefix = 'zikulamultihookmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the list of entries
        $url = $this->router->generate($routePrefix . 'view');
    
        return $url;
    }

    /**
     * @inheritDoc
     */
    public function handleCommand(array $args = [])
    {
        $result = parent::handleCommand($args);
        if (false === $result) {
            return $result;
        }
    
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if ('create' == $this->templateParameters['mode'] && $this->form->has('submitrepeat') && $this->form->get('submitrepeat')->isClicked()) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * @inheritDoc
     */
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        $message = '';
        switch ($args['commandName']) {
            case 'submit':
                if ('create' == $this->templateParameters['mode']) {
                    $message = $this->__('Done! Entry created.');
                } else {
                    $message = $this->__('Done! Entry updated.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Entry deleted.');
                break;
            default:
                $message = $this->__('Done! Entry updated.');
                break;
        }
    
        return $message;
    }

    /**
     * @inheritDoc
     * @throws RuntimeException Thrown if concurrent editing is recognised or another error occurs
     */
    public function applyAction(array $args = [])
    {
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
    
        $success = false;
        $flashBag = $this->requestStack->getCurrentRequest()->getSession()->getFlashBag();
        try {
            // execute the workflow action
            $success = $this->workflowHelper->executeAction($entity, $action);
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . ' ' . $exception->getMessage());
            $logArgs = ['app' => 'ZikulaMultiHookModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => 'entry', 'id' => $entity->getKey(), 'errorMessage' => $exception->getMessage()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', $logArgs);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && 'create' == $this->templateParameters['mode']) {
            // store new identifier
            $this->idValue = $entity->getKey();
        }
    
        return $success;
    }

    /**
     * Get URL to redirect to.
     *
     * @param array $args List of arguments
     *
     * @return string The redirect url
     */
    protected function getRedirectUrl(array $args = [])
    {
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if ($session->has('zikulamultihookmodule' . $this->objectTypeCapital . 'Referer')) {
            $this->returnTo = $session->get('zikulamultihookmodule' . $this->objectTypeCapital . 'Referer');
            $session->remove('zikulamultihookmodule' . $this->objectTypeCapital . 'Referer');
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = substr($this->returnTo, 0, 5) == 'admin' ? 'admin' : '';
        $routePrefix = 'zikulamultihookmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'userIndex':
            case 'adminIndex':
                return $this->router->generate($routePrefix . 'index');
            case 'userView':
            case 'adminView':
                return $this->router->generate($routePrefix . 'view');
            case 'userOwnView':
            case 'adminOwnView':
                return $this->router->generate($routePrefix . 'view', [ 'own' => 1 ]);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}