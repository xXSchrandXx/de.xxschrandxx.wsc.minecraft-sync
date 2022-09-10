<?php

namespace wcf\data\user\group\minecraft;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * MinecraftGroup Action class
 *
 * @author   xXSchrandXx
 * @package  WoltLabSuite\Core\Data\User\Group\Minecraft
 */
class MinecraftGroupAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = MinecraftGroupEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.minecraftSync.canManage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.minecraftSync.canManage'];
}
