<?php

namespace wcf\system\event\listener;

use wcf\acp\form\UserGroupEditForm;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncBackgroundJob;
use wcf\system\exception\SystemException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;
use wcf\util\JSON;

class MinecraftAcpGroupAddListener implements IParameterizedEventListener
{
    /**
     * Liste der Minecraft Server Gruppen
     *
     * @var array
     */
    protected $minecraftGroups = [];

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!(MINECRAFT_SYNC_ENABLED && MINECRAFT_LINKER_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.minecraftSync.canManage')) {
            return;
        }

        $this->$eventName($eventObj);
    }

    /**
     * @see AbstractForm::readFormparameters()
     */
    public function readFormParameters()
    {
        if (isset($_POST['minecraftGroupNames'])) {
            $this->minecraftGroups = $_POST['minecraftGroupNames'];
        }
    }

    /**
     * @see AbstractForm::validate()
     */
    public function validate($eventObj)
    {
        foreach (MinecraftSyncHandler::getInstance()->getMinecrafts() as $minecraft) {
            // TODO
        }
    }

    /**
     * @see AbstractForm::save()
     */
    public function save($eventObj)
    {
        $eventObj->additionalFields = array_merge($eventObj->additionalFields, [
            'minecraftGroups' => JSON::encode($this->minecraftGroups)
        ]);

        if (MINECRAFT_SYNC_ENABLED) {
            BackgroundQueueHandler::getInstance()->enqueueIn(new MinecraftSyncBackgroundJob());
        }

        // // reset values
        if (!($eventObj instanceof UserGroupEditForm)) {
            $this->minecraftGroups = [];
        }
    }

    /**
     * @see AbstractForm::assignVariables()
     */
    public function assignVariables($eventObj)
    {
        if (empty($_POST) && $eventObj instanceof UserGroupEditForm) {
            try {
                $this->minecraftGroups = JSON::decode($eventObj->group->minecraftGroups);
            } catch (SystemException $e) {
                // do nothing
            }
        }

        $minecraft = MinecraftSyncHandler::getInstance();

        // assign variables
        WCF::getTPL()->assign(
            [
                'minecrafts' => $minecraft->getMinecrafts(),
                'minecraftGroups' => $this->minecraftGroups,
                'minecraftGroupNames' => $minecraft->groupList()
            ]
        );
    }
}
