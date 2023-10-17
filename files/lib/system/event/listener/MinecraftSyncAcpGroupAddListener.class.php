<?php

namespace wcf\system\event\listener;

use wcf\data\minecraft\MinecraftList;
use wcf\data\user\group\minecraft\MinecraftGroupList;
use wcf\system\WCF;
use wcf\util\StringUtil;

class MinecraftSyncAcpGroupAddListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     * @param \wcf\acp\form\UserGroupEditForm $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!(MINECRAFT_SYNC_ENABLED && MINECRAFT_LINKER_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.minecraftSync.canManage')) {
            return;
        }
        if (!($eventObj instanceof \wcf\acp\form\UserGroupEditForm)) {
            return;
        }

        $minecraftList = new MinecraftList();
        $minecraftList->setObjectIDs(explode("\n", StringUtil::unifyNewlines(MINECRAFT_SYNC_IDENTITY)));
        $minecraftList->readObjects();
        $minecrafts = $minecraftList->getObjects();

        /** @var \wcf\data\user\group\minecraft\MinecraftGroup[] */
        $minecraftGroups = [];
        foreach ($minecrafts as $minecraftID => $minecraft) {
            $minecraftGroupList = new MinecraftGroupList();
            $minecraftGroupList->getConditionBuilder()->add('groupID = ? AND minecraftID = ?', [$eventObj->groupID, $minecraftID]);
            $minecraftGroupList->readObjects();
            $minecraftGroups[$minecraftID] = $minecraftGroupList->getObjects();
        }

        WCF::getTPL()->assign(
            [
                'groupID' => $eventObj->groupID,
                'minecrafts' => $minecrafts,
                'minecraftGroups' => $minecraftGroups
            ]
        );
    }
}
