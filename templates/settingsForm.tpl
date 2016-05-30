{**
 * @file plugins/generic/seriesOverview/templates/settingsForm.tpl
 *
 * Copyright (c) 2016 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 *}

<div id="seriesOverviewSettings">

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#seriesOverviewSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="seriesOverviewSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="seriesOverviewSettingsFormNotification"}

	<h3>{translate key="plugins.generic.seriesOverview.settings"}</h3>

	{fbvFormArea id="seriesOverviewSettingsFormArea"}

		{fbvFormSection list=true}
			<span>{translate key="plugins.generic.seriesOverview.settings.pathIntro"}</span>
			{fbvElement type="text" id="path" value=$path label="plugins.generic.seriesOverview.settings.path" size=$fbvStyles.size.SMALL}
		{/fbvFormSection}

		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="useImages" name="useImages" value="1" checked=$useImages  label="plugins.generic.seriesOverview.settings.useImages"}
			{fbvElement type="checkbox" id="imageOnSeriesPages" name="imageOnSeriesPages" value="1" checked=$imageOnSeriesPages  label="plugins.generic.seriesOverview.settings.imageOnSeriesPages"}

		{/fbvFormSection}

	{/fbvFormArea}

	{fbvFormButtons}
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
