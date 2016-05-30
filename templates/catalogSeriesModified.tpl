{**
 * @file plugins/generic/seriesOverview/templates/catalogSeriesModified.tpl
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *}

<link rel="stylesheet" href="{$baseUrl}/plugins/generic/seriesOverview/css/seriesOverview.css" type="text/css" />

{include file="frontend/components/header.tpl" pageTitleTranslated=$series->getLocalizedTitle()}

<div class="page page_catalog_series">

	{* Breadcrumb *}
	{**{include file="frontend/components/breadcrumbs_catalog.tpl" type="series" currentTitle=$series->getLocalizedTitle()}**}

	{* Count of monographs in this series *}
	{**<div class="monograph_count">
		{translate key="catalog.browseTitles" numTitles=$publishedMonographs|@count}
	</div>**}

	{* Image and description *}
	{assign var="image" value=$series->getImage()}
	{assign var="description" value=$series->getLocalizedDescription()|strip_unsafe_html}
	<div class="about_section{if $image} has_image{/if}{if $description} has_description{/if}">
		{if $image}
		{if $imageOnSeriesPages}
			<div class="cover" href="{url router=$smarty.const.ROUTE_PAGE page="catalog" op="fullSize" type="series" id=$series->getId()}">
				<img src="{url router=$smarty.const.ROUTE_PAGE page="catalog" op="thumbnail" type="series" id=$series->getId()}" alt="{$series->getLocalizedTitle()|escape}" />
			</div>
		{/if}
		{/if}
		<div class="description">
			{$description|strip_unsafe_html}
		</div>
		{if $series->getOnlineISSN()}
			<div class="onlineISSN">
				{translate key="catalog.manage.series.onlineIssn"} {$series->getOnlineISSN()|escape}
			</div>
		{/if}
		{if $series->getPrintISSN()}
			<div class="printISSN">
				{translate key="catalog.manage.series.printIssn"} {$series->getPrintISSN()|escape}
			</div>
		{/if}
	</div>

	{* No published titles in this category *}
	{if empty($publishedMonographs)}
		<h2>
			{translate key="catalog.allBooks"}
		</h2>
		<p>{translate key="catalog.noTitlesSection"}</p>

	{else}

		{* New releases *}
		{if !empty($newReleasesMonographs)}
			{include file="frontend/components/monographList.tpl" monographs=$newReleasesMonographs titleKey="catalog.newReleases"}
		{/if}

		{* All monographs *}
		{include file="frontend/components/monographList.tpl" monographs=$publishedMonographs featured=$featuredMonographIds titleKey="catalog.allBooks"}

	{/if}

</div><!-- .page -->

{include file="common/frontend/footer.tpl"}
