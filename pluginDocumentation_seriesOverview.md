Key data
============

- name of the plugin: Series Overview Plugin
- author: Carola Fanselow
- current version: 1.0.0.0
- tested on OMP version: 1.2.0
- github link: https://github.com/langsci/seriesOverview.git
- community plugin: yes, 1.0.0.0
- date: 2016/05/17

Description
============

This plugin lists all series and all books in each series. The order of the series is determined by the number of books in the series. The books are presented in alphabetical order. In the settings, you can choose:
a) whether images are to be displayed in the accordion content area next to the book list and as icons for the series list. If there are books in a series, the cover of the most recent book is used (dates are taken from section "Publication dates", role "Publication date", publication format "PDF). If there are no books in a series or the date is not set in the appropriate way, the series covers are used.
b) wether to display the series cover on the individual series pages
This is the LangSci version of this plugin: The style of the series page is slightly adapted (hide breadcrumbs, bigger headings, etc.).
 
Implementation
================

Hooks
-----
- used hooks: 2

		LoadHandler
		TemplateManager::display

New pages
------
- new pages: 1

		[press]/[path to be specified in the plugin settings]

Templates
---------
- templates that replace other templates: 1

		catalogSeriesModified.tpl replaces frontend/pages/catalogSeries.tpl

- templates that are modified with template hooks: 0
- new/additional templates: 1

		seriesOverview.tpl

Database access, server access
-----------------------------
- reading access to OMP tables: 9

		press_settings
		series
		submissions
		published_submissions
		user_groups
		user_group_settings
		publication_dates
		publication_formats
		publication_format_settings

- writing access to OMP tables: 0
- new tables: 0
- nonrecurring server access: no
- recurring server access: no
 
Classes, plugins, external software
-----------------------
- OMP classes used (php): 7
	
		GenericPlugin
		Handler
		DAO
		PublishedMonographDAO
		MonographDAO
		SeriesDAO
		Form

- OMP classes used (js, jqeury, ajax): 1

		AjaxFormHandler

- necessary plugins: 0
- optional plugins: 0
- use of external software: no
- file upload: no
 
Metrics
--------
- number of files: 15
- lines of code: 1297

Settings
--------
- settings: 3

		path to the series overview page
		wether to user images or not
		wether to display images on the individual series pages

Plugin category
----------
- plugin category: generic

Other
=============
- does using the plugin require special (background)-knowledge?: no
- access restrictions: no
- adds css: yes




