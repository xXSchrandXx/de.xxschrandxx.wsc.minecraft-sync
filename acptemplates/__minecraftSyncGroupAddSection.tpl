{if MINECRAFT_SYNC_ENABLED && MINECRAFT_SYNC_IDENTITY}
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.acp.group.minecraftSection.minecraftSync.sectionTitle{/lang}</h2>

		{if $minecrafts|count > 0}
			<div class="section tabMenuContainer" data-active="minecraft-sync" data-store="activeTabMenuItem">
				<div id="minecraft-sync" class="tabMenuContainer tabMenuContent">
					<nav class="menu">
						<ul>
							{foreach from=$minecrafts item=minecraft}
								<li>
									<a href="#minecraft-sync-{$minecraft->getObjectID()}">{$minecraft->name}</a>
								</li>
							{/foreach}
						</ul>
					</nav>
					{foreach from=$minecrafts item=minecraft}
						<div id="minecraft-sync-{$minecraft->getObjectID()}" class="tabMenuContent hidden"
							data-name="minecraft-sync-{$minecraft->getObjectID()}" data-object-id="{$minecraft->getObjectID()}">
							<div class="section">
								<nav class="contentHeaderNavigation">
									<ul>
										<li>
											<a href="{link controller='MinecraftGroupAdd' id=$groupID minecraftID=$minecraft->getObjectID()}{/link}" class="button">
												<span class="icon icon16 fa-plus"></span>
												<span>{lang}wcf.acp.form.minecraftGroupAdd.formTitle.add{/lang}</span>
											</a>
										</li>
										{event name='contentHeaderNavigation'}
									</ul>
								</nav>
								{if $minecraftGroups[$minecraft->getObjectID()]|count > 0}
									<div class="section tabularBox">
										<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\user\group\minecraft\MinecraftGroupAction">
											<thead>
												<tr>
													<th></th>
													<th>{lang}wcf.global.objectID{/lang}</th>
													<th>{lang}wcf.acp.group.minecraftSection.minecraftSync.list.minecraftName{/lang}</th>
													<th>{lang}wcf.acp.group.minecraftSection.minecraftSync.list.shouldHave{/lang}</th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$minecraftGroups[$minecraft->minecraftID] item=minecraftGroup}
													<tr class="jsObjectActionObject" data-object-id="{@$minecraftGroup->getObjectID()}">
														<td class="columnIcon">
															<a href="{link controller='MinecraftGroupEdit' id=$minecraftGroup->getObjectID()}{/link}"
																title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
																<span class="icon icon16 fa-pencil"></span>
															</a>
															{objectAction action="delete" objectTitle=$minecraftGroup->getGroupName()}
														</td>
														<td class="columnID">{#$minecraftGroup->getObjectID()}</td>
														<td class="columnText">{$minecraftGroup->getGroupName()}</td>
														<td class="columnStatus">
															{if $minecraftGroup->getShouldHave()}
																{lang}wcf.acp.option.type.boolean.yes{/lang}
															{else}
																{lang}wcf.acp.option.type.boolean.no{/lang}
															{/if}
														</td>
													</tr>
												{/foreach}
										</tbody>
									</table>
								</div>
								{else}
									<p class="info">{lang}wcf.global.noItems{/lang}</p>
								{/if}
							</div>
						</div>
					{/foreach}
				</div>
			</div>
		{else}
			<p class="info">{lang}wcf.global.noItems{/lang}</p>
		{/if}
	</section>
{/if}