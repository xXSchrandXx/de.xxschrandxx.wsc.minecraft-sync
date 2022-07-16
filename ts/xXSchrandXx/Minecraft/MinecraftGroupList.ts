import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";
import * as Language from "WoltLabSuite/Core/Language";

export class MinecraftGroupList {
    public constructor() {
        const elements = document.getElementsByClassName("minecraftGroupListButton");
        for (let i = 0; i < elements.length; i++) {
            const element = elements[i] as HTMLElement;
            element.addEventListener('click', (event: Event) => this._click(event));
        }
    }

    public _click(event: Event): void {
        event.preventDefault();

        var element = event['path'][5] as HTMLElement;
        var objectID = element.getAttribute('data-object-id') as string;

        Ajax.api({
            _ajaxSetup: () => {
                return {
                    data: {
                        actionName: "groupList",
                        className: "wcf\\data\\minecraft\\MinecraftSyncAction",
                        objectIDs: [objectID]
                    }
                };
            },
            _ajaxSuccess: (data: DatabaseObjectActionResponse) => {
                // TODO recreate list and don't refresh
                UiNotification.show(Language.get('wcf.global.success'), () => {
                    window.location.reload();
                });
            }
        });
    }
}

export default MinecraftGroupList;