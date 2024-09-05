<?php

use wcf\event\endpoint\ControllerCollecting;
use wcf\system\endpoint\controller\xxschrandxx\minecraft\sync\GetGroups;
use wcf\system\event\EventHandler;

return static function (): void {
    EventHandler::getInstance()->register(
        ControllerCollecting::class,
        static function (ControllerCollecting $event) {
            $event->register(new GetGroups());
        }
    );
};
