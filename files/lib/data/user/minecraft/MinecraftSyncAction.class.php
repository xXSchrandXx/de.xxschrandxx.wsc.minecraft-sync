<?php

namespace wcf\data\user\minecraft;

use wcf\system\exception\PermissionDeniedException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;
use wcf\util\JSON;

class MinecraftSyncAction extends MinecraftUserAction
{
    /**
     * list of permissions required sync objects
     * @var string[]
     */
    protected $permissionsSync = ['user.minecraftLinker.canManage'];

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
        $handler = MinecraftSyncHandler::getInstance();
        foreach ($this->getObjectIDs() as $objectID) {
            $minecraftUser = new MinecraftUser($objectID);
            $response[$objectID] = JSON::encode($handler->sync($minecraftUser));
        }
        return $response;
    }
}
