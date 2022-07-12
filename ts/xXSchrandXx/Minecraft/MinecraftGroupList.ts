import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import * as Language from "WoltLabSuite/Core/Language";
import { setTitle } from "WoltLabSuite/Core/Ui/Dialog";

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

        var element = event['path'][3] as HTMLElement;
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
                }, JSON.stringify(data['returnValues'][objectID]));
            }
        });
    }
}

export default MinecraftGroupList;