<?php

namespace wcf\data\user\minecraft;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\minecraft\MinecraftEditor;
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

    /**
     * Returns a list of currently loaded objects.
     *
     * @return MinecraftUserEditor[]
     */
    public function getObjects()
    {
        return parent::getObjects();
    }

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
            throw new UserInputException('objectID');
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

        if (empty($this->getObjects())) {
            throw new UserInputException('objectID');
        }
    }

    public function groupList()
    {
        $groups = [];
        foreach ($this->getObjects() as $object) {
            $groups[$object->objectID] = MinecraftSyncHandler::getInstance()->groupList($object->objectID);
            $editor = new MinecraftEditor($object);
            $editor->update([
                'groups' => $groups[$object->objectID]
            ]);
        }
        return $groups;
    }
}
