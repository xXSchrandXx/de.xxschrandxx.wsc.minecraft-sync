<?php

namespace wcf\data\user\minecraft;

use wcf\system\exception\PermissionDeniedException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;

class MinecraftUserSyncAction extends MinecraftUserAction
{
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
    }

    public function sync()
    {
        $response = [];
        foreach ($this->getObjectIDs() as $objectID) {
            $minecraftUser = new MinecraftUser($objectID);
            $response[$objectID] = MinecraftSyncHandler::getInstance()->sync($minecraftUser);
        }
        return $response;
    }
}
