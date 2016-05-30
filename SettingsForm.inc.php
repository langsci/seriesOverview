<?php

/**
 * @file plugins/generic/seriesOverview/SettingsForm.inc.php
 *
 * Copyright (c) 2015 Language Science Press
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SettingsForm
 */

import('lib.pkp.classes.form.Form');

class SettingsForm extends Form {

	/** @var int Associated context ID */
	private $_contextId;

	/** @var WebFeedPlugin Web feed plugin */
	private $_plugin;

	/**
	 * Constructor
	 * @param $plugin WebFeedPlugin Web feed plugin
	 * @param $contextId int Context ID
	 */
	function SettingsForm($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');
		$this->addCheck(new FormValidatorPost($this));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$contextId = $this->_contextId;
		$plugin = $this->_plugin;

		$this->setData('path', $plugin->getSetting($contextId, 'path'));
		$this->setData('useImages', $plugin->getSetting($contextId, 'useImages'));
		$this->setData('imageOnSeriesPages', $plugin->getSetting($contextId, 'imageOnSeriesPages'));
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('path', 'useImages','imageOnSeriesPages'));

		// check that recent items value is a positive integer
		//if ((int) $this->getData('recentItems') <= 0) $this->setData('recentItems', '');

		$this->addCheck(new FormValidator($this, 'path', 'required', 'plugins.generic.seriesOverview.settings.recentItemsRequired'));

	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request);
	}

	/**
	 * Save settings. 
	 */
	function execute() {
		$plugin = $this->_plugin;
		$contextId = $this->_contextId;

		$plugin->updateSetting($contextId, 'path', $this->getData('path'));
		$plugin->updateSetting($contextId, 'useImages', $this->getData('useImages'));
		$plugin->updateSetting($contextId, 'imageOnSeriesPages', $this->getData('imageOnSeriesPages'));
	}
}

?>
