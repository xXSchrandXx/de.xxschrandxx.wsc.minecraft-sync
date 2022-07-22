<span class="minecraftSyncButton icon icon16 fa-refresh pointer jsToolTip"
	title="{lang}wcf.page.minecraftSyncUserAdd.button.status{/lang}">
</span>

<script data-relocate="true">
	require(["xXSchrandXx/Minecraft/MinecraftUserSync", "Language"], function(MinecraftUserSync, Language) {
		Language.addObject({
			'wcf.page.minecraftSyncUserAdd.button.status.result': '{lang}wcf.page.minecraftSyncUserAdd.button.status.result{/lang}'
		});
		new MinecraftUserSync.default();
	});
</script>