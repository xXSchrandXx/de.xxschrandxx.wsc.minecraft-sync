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

        var element = event['path'][2] as HTMLElement;
        var objectID = element.getAttribute('data-object-id') as string;

        console.log(objectID);

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
                UiDialog.open({
                    _dialogSetup: () => {
                        return {
                            id: 'minecraftSyncDialog',
                            source: null,
                            options: {
                                onShow: function(): void {
                                    setTitle('minecraftSyncDialog', Language.get('wcf.page.minecraftSyncUserAdd.button.status.result'));
                                }
                            }
                        }
                    }
                }, data['returnValues'][objectID]);
            }
        });
    }
}

export default MinecraftUserSync;