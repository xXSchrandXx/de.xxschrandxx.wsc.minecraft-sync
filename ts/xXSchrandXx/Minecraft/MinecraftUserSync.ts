import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import * as Language from "WoltLabSuite/Core/Language";
import { setTitle } from "WoltLabSuite/Core/Ui/Dialog";

export class MinecraftUserSync {
    public constructor() {
        const elements = document.getElementsByClassName("minecraftSyncButton");
        for (let i = 0; i < elements.length; i++) {
            const element = elements[i] as HTMLElement;
            element.addEventListener('click', (event: Event) => this._click(event));
        }
    }

    public _click(event: Event): void {
        event.preventDefault();

        var element = event['path'][3] as HTMLElement;
        var objectID = element.getAttribute('data-object-id') as string;

        Ajax.api({
            _ajaxSetup: () => {
                return {
                    data: {
                        actionName: "sync",
                        className: "wcf\\data\\user\\minecraft\\MinecraftUserSyncAction",
                        objectIDs: [objectID],
                    }
                };
            },
            _ajaxSuccess: (data: DatabaseObjectActionResponse) => {
                // TODO reload page
            }
        });
    }
}

export default MinecraftUserSync;