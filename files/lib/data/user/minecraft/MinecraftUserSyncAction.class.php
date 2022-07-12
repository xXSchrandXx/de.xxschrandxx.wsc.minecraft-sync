<?php

namespace wcf\data\user\minecraft;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;

class MinecraftUserSyncAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = MinecraftUserEditor::class;

    /**
     * list of permissions required sync objects
     * @var string[]
     */
    protected $permissionsSync = ['admin.minecraftSync.canManage'];

    public function validateSync()
    {
        // validate permissions
        if (\is_array($this->permissionsSync) && !empty($this->permissionsSync)) {
            WCF::getSession()->checkPermissions($this->permissionsSync);
        } else {
            throw new PermissionDeniedException();
        }

        if (empty($this->getObjects())) {
            $this->readObjects();
        }

        if (empty($this->getObjects())) {
            throw new UserInputException('objectIDs');
        }
    }

    public function sync()
    {
        $response = [];
        foreach ($this->getObjects() as $editor) {
            $response[$editor->getObjectID()] = MinecraftSyncHandler::getInstance()->sync($editor->getDecoratedObject());
        }
        return $response;
    }

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

        wcfDebug($this->getObjectIDs(), $this->getObjects());

        if (empty($this->getObjects())) {
            throw new UserInputException('objectIDs');
        }
    }

    public function groupList()
    {
        $groups = [];
        foreach ($this->getObjects() as $editor) {
            $groups[$editor->objectID] = MinecraftSyncHandler::getInstance()->groupList($editor->objectID);
            $editor->update([
                'groups' => $groups[$editor->objectID]
            ]);
        }
        return $groups;
    }
}
