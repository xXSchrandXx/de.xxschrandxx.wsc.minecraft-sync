<?php

namespace wcf\acp\form;

use wcf\data\minecraft\Minecraft;
use wcf\data\minecraft\MinecraftList;
use wcf\data\user\group\minecraft\MinecraftGroup;
use wcf\data\user\group\minecraft\MinecraftGroupAction;
use wcf\data\user\group\minecraft\MinecraftGroupList;
use wcf\data\user\group\UserGroup;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * MinecraftGroup add acp form class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftGroupAddForm extends AbstractFormBuilderForm
{
    /**
     * @var \wcf\data\user\minecraft\MinecraftGroup
     */
    public $formObject;

    /**
     * @inheritDoc
     */
    public $neededModules = ['MINECRAFT_SYNC_ENABLED','MINECRAFT_SYNC_IDENTITY'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.minecraftSync.canManage'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.list';

    /**
     * @inheritDoc
     */
    public $objectActionClass = MinecraftGroupAction::class;

    /**
     * @var Minecraft
     */
    protected $minecraft;

    /**
     * @var UserGroup
     */
    protected $group;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if ($this->formAction == 'create') {
            $groupID = 0;
            if (isset($_REQUEST['id'])) {
                $groupID = (int)$_REQUEST['id'];
            }
            $this->group = new UserGroup($groupID);
            if (!$this->group->getObjectID()) {
                throw new IllegalLinkException();
            }
            $minecraftID = 0;
            if (isset($_REQUEST['minecraftID'])) {
                $minecraftID = (int)$_REQUEST['minecraftID'];
            }
            $this->minecraft = new Minecraft($minecraftID);
            if (!$this->minecraft->getObjectID()) {
                throw new IllegalLinkException();
            }
        } else {
            $minecraftGroupID = 0;
            if (isset($_REQUEST['id'])) {
                $minecraftGroupID = (int)$_REQUEST['id'];
            }
            $this->formObject = new MinecraftGroup($minecraftGroupID);
            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
            $this->group = new UserGroup($this->formObject->getGroupID());
            $this->minecraft = new Minecraft($this->formObject->getMinecraftID());
        }
    }

    /**
     * @inheritDoc
     */
    public function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('minecraftName')
                        ->required()
                        ->label('wcf.acp.form.minecraftGroupAdd.minecraftName')
                        ->description('wcf.acp.form.minecraftGroupAdd.minecraftName.description')
                        ->maximumLength(30)
                        ->addValidator(new FormFieldValidator('duplicate', function (TextFormField $field) {
                            if ($this->formAction == 'edit' && $field->getValue() == $this->formObject->getGroupName()) {
                                return;
                            }
                            $minecraftGroupList = new MinecraftGroupList();
                            $minecraftGroupList->getConditionBuilder()->add('minecraftName = ? AND minecraftID = ? AND groupID = ?', [$field->getValue(), $this->minecraft->getObjectID(), $this->group->getObjectID()]);
                            if ($minecraftGroupList->countObjects() > 0) {
                                $field->addValidationError(
                                    new FormFieldValidationError(
                                        'duplicate',
                                        'wcf.acp.form.minecraftGroupAdd.minecraftName.error.duplicate'
                                    )
                                );
                            }
                        })),
                    BooleanFormField::create('shouldHave')
                        ->label('wcf.acp.form.minecraftGroupAdd.shouldHave')
                        ->description('wcf.acp.form.minecraftGroupAdd.shouldHave.description')
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction == 'edit') {
            parent::save();
            return;
        }
        $this->additionalFields['minecraftID'] = $this->minecraft->getObjectID();
        $this->additionalFields['groupID'] = $this->group->getObjectID();

        parent::save();
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'minecraftID' => $this->minecraft->getObjectID(),
            'groupID' => $this->group->getObjectID()
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction()
    {
        if ($this->formAction == 'create') {
            $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, ['id' => $this->group->getObjectID(), 'minecraftID' => $this->minecraft->getObjectID()]));
        } else {
            parent::setFormAction();
        }
    }
}
