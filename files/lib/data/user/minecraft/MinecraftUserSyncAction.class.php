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
            $response[$editor->getObjectID()] = MinecraftSyncHandler::getInstance()->sync($editor);
        }
        return $response;
    }
}
