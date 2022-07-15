<?php

namespace wcf\data\minecraft;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;
use wcf\util\JSON;

class MinecraftSyncAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = MinecraftEditor::class;

    /**
     * list of permissions required get groups objects
     * @var string[]
     */
    protected $permissionsGroupList = ['admin.minecraftSync.canManage'];

    public function validateGroupList()
    {
        // validate permissions
        if (\is_array($this->permissionsGroupList) && !empty($this->permissionsGroupList)) {
            WCF::getSession()->checkPermissions($this->permissionsGroupList);
        } else {
            throw new PermissionDeniedException();
        }

        if (empty($this->getObjects())) {
            $this->readObjects();
        }

        if (empty($this->getObjects())) {
            throw new UserInputException('objectIDs', 'empty', ['objectIDs' => $this->getObjectIDs()]);
        }
    }

    public function groupList()
    {
        foreach ($this->getObjects() as $editor) {
            $editor->update([
                'groups' => \serialize(MinecraftSyncHandler::getInstance()->groupList($editor->objectID)[$editor->getObjectID()])
            ]);
        }
    }
}
