	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.page.groupAddSection.minecraftSync.sectionTitle{/lang}</h2>

		{if $minecrafts|count > 0}
			<div class="section tabMenuContainer" data-active="minecraft-sync" data-store="activeTabMenuItem">
				<div id="minecraft-sync" class="tabMenuContainer tabMenuContent">
					<nav class="menu">
						<ul>
							{foreach from=$minecrafts item=minecraft}
								<li>
									<a href="#minecraft-sync-{$minecraft->minecraftID}">{$minecraft->connectionName}</a>
								</li>
							{/foreach}
						</ul>
					</nav>
					{foreach from=$minecrafts item=minecraft}
						<div id="minecraft-sync-{$minecraft->minecraftID}" class="tabMenuContent hidden" data-name="minecraft-sync-{$minecraft->minecraftID}">
							<div class="section">
								{include file='minecraftSyncUserGroupTabSection' minecraftID=$minecraft->minecraftID}
							</div>
						</div>
					{/foreach}
				</div>
			</div>
		{else}
			<p class="info">{lang}wcf.global.noItems{/lang}</p>
		{/if}
	</section>