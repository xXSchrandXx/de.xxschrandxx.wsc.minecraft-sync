import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import { setTitle } from "WoltLabSuite/Core/Ui/Dialog";

export class MinecraftSync {
    public constructor() {
        const elements = document.getElementsByClassName("minecraftSyncButton");
        for (let i = 0; i < elements.length; i++) {
            const element = elements[i] as HTMLElement;
            element.addEventListener('click', (event: Event) => this._click(event));
        }
    }

    public _click(event: Event): void {
        event.preventDefault();

        var element = event['path'][1] as HTMLElement;
        var objectID = element.getAttribute('data-object-id') as string;

        Ajax.api({
            _ajaxSetup: () => {
                return {
                    data: {
                        actionName: "sync",
                        className: "wcf\\data\\user\\minecraft\\MinecraftSyncAction",
                        objectIDs: [objectID]
                    }
                };
            },
            _ajaxSuccess: (data: DatabaseObjectActionResponse) => {
                console.log("Received response", data);
                UiDialog.open({
                    _dialogSetup: () => {
                        return {
                            id: "string",
                            source: null,
                            options: {
                                onShow: function(): void {
                                    setTitle("string", "Result");
                                }
                            }
                        }
                    }
                }, data['returnValues'][objectID]);
            }
        });
    }
}

export default MinecraftSync;