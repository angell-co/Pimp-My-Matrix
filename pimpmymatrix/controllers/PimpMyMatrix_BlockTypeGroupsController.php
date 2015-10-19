<?php
namespace Craft;

/**
 * Pimp My Matrix by Supercool
 *
 * @package   PimpMyMatrix
 * @author    Josh Angell
 * @copyright Copyright (c) 2014, Supercool Ltd
 * @link      http://www.supercooldesign.co.uk
 */

class PimpMyMatrix_BlockTypeGroupsController extends BaseController
{

	/**
	 * @inheritDoc BaseController::init()
	 *
	 * @throws HttpException
	 * @return null
	 */
	public function init()
	{
		craft()->userSession->requireAdmin();
	}


	/**
	 * Saves a layout
	 */
	public function actionSaveBlockTypeGroups()
	{

		$this->requirePostRequest();

		// This will be an array of Tab Names with Block Type IDs.
		// The order in which they appear is the order in which they should also
		// be returned in eventually, so we will just rely on the id to describe this
		// and make sure each time we are referencing a context that already exists to
		// delete the rows matching that context before proceeding with the save.
		$blockTypeGroupsPostData = craft()->request->getPost('blockTypeGroups');

		$context = craft()->request->getPost('context');

		// Remove all current group rows by context
		craft()->pimpMyMatrix_blockTypeGroups->deleteBlockTypeGroupsByContext($context);

		// Loop over the data and save new rows for each block type / group combo
		foreach ($blockTypeGroupsPostData as $tabName => $blockTypeIds)
		{

			foreach ($blockTypeIds as $blockTypeId)
			{
				$blockTypeGroup = new PimpMyMatrix_BlockTypeGroupModel();
				$blockTypeGroup->matrixBlockTypeId = $blockTypeId;
				$blockTypeGroup->tabName           = urldecode($tabName);
				$blockTypeGroup->context           = $context;

				// TODO: Catch errors
				craft()->pimpMyMatrix_blockTypeGroups->saveBlockTypeGroup($blockTypeGroup);
			}

		}

		$this->returnJson(array(
			'success' => true
		));

	}


	// /**
	//  * Deletes a group
	//  */
	// public function actionDeletePerformanceGroup()
	// {
	// 	$this->requirePostRequest();
	// 	$this->requireAjaxRequest();
	//
	// 	$performanceGroupId = craft()->request->getRequiredPost('id');
	//
	// 	craft()->boxOffice_performanceGroups->deletePerformanceGroupById($performanceGroupId);
	// 	$this->returnJson(array('success' => true));
	// }

	//
	// /**
	//  * [editFieldLayout description]
	//  */
	// public function actionEditFieldLayout(array $variables = array())
	// {
	//
	// 	// get the performance group
	// 	if (!empty($variables['performanceGroupId']))
	// 	{
	//
	// 		$variables['performanceGroup'] = craft()->boxOffice_performanceGroups->getPerformanceGroupById($variables['performanceGroupId']);
	//
	// 		if (!$variables['performanceGroup'])
	// 		{
	// 			throw new HttpException(404);
	// 		}
	//
	// 		$variables['title'] = $variables['performanceGroup']->name . ' — ' . $variables['fieldLayoutType'];
	// 	}
	// 	else
	// 	{
	// 		throw new HttpException(404);
	// 	}
	//
	//
	// 	// Get all the field layouts for this group
	// 	$fieldLayoutIds = craft()->db->createCommand()
	// 		->select('fieldLayoutId')
	// 		->from('boxoffice_fieldlayouts')
	// 		->where('performanceGroupId = :performanceGroupId', array(':performanceGroupId' => $variables['performanceGroup']->id))
	// 		->queryColumn();
	//
	// 	// Get the actual performance or instance field layout for this group
	// 	// depending on the url we're on, and the weird BoxOffice_FieldLayout as well
	// 	if ($fieldLayoutIds)
	// 	{
	// 		switch ($variables['fieldLayoutType']) {
	// 			case 'performances':
	// 				$fieldLayoutType = BoxOfficeElementType::Performance;
	// 				break;
	//
	// 			case 'instances':
	// 				$fieldLayoutType = BoxOfficeElementType::Instance;
	// 				break;
	// 		}
	//
	// 		$fieldLayoutId = craft()->db->createCommand()
	// 			->select('id')
	// 			->from('fieldlayouts')
	// 			->where('type = :type', array(':type' => $fieldLayoutType))
	// 			->andWhere(array('and', array('in', 'id', $fieldLayoutIds)))
	// 			->queryScalar();
	// 	}
	//
	// 	$variables['fieldLayoutId'] = $fieldLayoutId;
	//
	// 	$variables['fieldLayout'] = null;
	//
	// 	if ( $fieldLayoutId )
	// 	{
	// 		$variables['fieldLayout'] = craft()->fields->getLayoutById($fieldLayoutId);
	//
	// 		$boxOfficeFieldLayoutId = craft()->db->createCommand()
	// 			->select('id')
	// 			->from('boxoffice_fieldlayouts')
	// 			->where('fieldLayoutId = :fieldLayoutId', array(':fieldLayoutId' => $fieldLayoutId))
	// 			->queryScalar();
	//
	// 		if ( $boxOfficeFieldLayoutId )
	// 		{
	// 			$variables['boxOfficeFieldLayoutId'] = $boxOfficeFieldLayoutId;
	// 		}
	// 	}
	//
	// 	$variables['crumbs'] = array(
	// 		array('label' => Craft::t('Box Office'), 'url' => UrlHelper::getUrl('boxoffice')),
	// 		array('label' => Craft::t('Performance Groups'), 'url' => UrlHelper::getUrl('boxoffice/performance-groups')),
	// 	);
	//
	// 	$this->renderTemplate('boxoffice/performance-groups/_edit-field-layout', $variables);
	// }
	//
	//
	// /**
	//  * [actionSaveFieldLayout description]
	//  */
	// public function actionSaveFieldLayout()
	// {
	//
	// 	$this->requirePostRequest();
	//
	// 	// Get box office field layout record
	// 	$boxOfficeFieldLayoutId = craft()->request->getPost('boxOfficeFieldLayoutId');
	//
	// 	if ($boxOfficeFieldLayoutId)
	// 	{
	//
	// 		$boxOfficeFieldLayoutRecord = BoxOffice_FieldLayoutRecord::model()->findById($boxOfficeFieldLayoutId);
	//
	// 		if (!$boxOfficeFieldLayoutRecord)
	// 		{
	// 			throw new Exception(Craft::t('No box office field layout exists with the ID “{id}”', array('id' => $boxOfficeFieldLayoutId)));
	// 		}
	//
	// 		$boxOfficeFieldLayout = BoxOffice_FieldLayoutModel::populateModel($boxOfficeFieldLayoutRecord);
	//
	// 	}
	//
	// 	$fieldLayoutType = craft()->request->getPost('fieldLayoutType');
	//
	// 	switch ($fieldLayoutType) {
	// 		case 'performances':
	// 			$boxOfficeFieldLayout->elementType = BoxOfficeElementType::Performance;
	// 			break;
	//
	// 		case 'instances':
	// 			$boxOfficeFieldLayout->elementType = BoxOfficeElementType::Instance;
	// 			break;
	// 	}
	//
	// 	// Set the field layout on the model
	// 	$postedFieldLayout = craft()->fields->assembleLayoutFromPost();
	// 	$boxOfficeFieldLayout->setFieldLayout($postedFieldLayout);
	//
	// 	// Save it
	// 	if (craft()->boxOffice_performanceGroups->saveFieldLayout($boxOfficeFieldLayout))
	// 	{
	// 		craft()->userSession->setNotice(Craft::t('Field layout saved.'));
	// 		$this->redirectToPostedUrl($boxOfficeFieldLayout);
	// 	}
	// 	else
	// 	{
	// 		craft()->userSession->setError(Craft::t('Couldn’t save field layout.'));
	// 	}
	//
	// 	// Send the fieldlayout back to the template
	// 	craft()->urlManager->setRouteVariables(array(
	// 		'boxOfficeFieldLayout' => $boxOfficeFieldLayout
	// 	));
	//
	// }
	//
	//
	// /**
	//  * Load the iCal settings view
	//  */
	// public function actionICalSettings(array $variables = array())
	// {
	//
	// 	// get the performance group
	// 	if (!empty($variables['performanceGroupId']))
	// 	{
	//
	// 		$variables['performanceGroup'] = craft()->boxOffice_performanceGroups->getPerformanceGroupById($variables['performanceGroupId']);
	//
	// 		if (!$variables['performanceGroup'])
	// 		{
	// 			throw new HttpException(404);
	// 		}
	//
	// 		$variables['title'] = $variables['performanceGroup']->name . ' — iCal Settings';
	// 	}
	// 	else
	// 	{
	// 		throw new HttpException(404);
	// 	}
	//
	// 	// Get all the field layouts for this group
	// 	$fieldLayoutIds = craft()->db->createCommand()
	// 		->select('fieldLayoutId')
	// 		->from('boxoffice_fieldlayouts')
	// 		->where('performanceGroupId = :performanceGroupId', array(':performanceGroupId' => $variables['performanceGroup']->id))
	// 		->queryColumn();
	//
	// 	// Get the actual performance field layout for this group
	// 	$variables['fieldLayoutId'] = craft()->db->createCommand()
	// 		->select('id')
	// 		->from('fieldlayouts')
	// 		->where('type = :type', array(':type' => BoxOfficeElementType::Performance))
	// 		->andWhere(array('and', array('in', 'id', $fieldLayoutIds)))
	// 		->queryScalar();
	//
	// 	// Get the actual instance field layout for this group
	// 	$variables['instanceFieldLayoutId'] = craft()->db->createCommand()
	// 		->select('id')
	// 		->from('fieldlayouts')
	// 		->where('type = :type', array(':type' => BoxOfficeElementType::Instance))
	// 		->andWhere(array('and', array('in', 'id', $fieldLayoutIds)))
	// 		->queryScalar();
	//
	// 	$variables['brandNewICalSettings'] = false;
	//
	// 	if (!empty($variables['performanceGroupId']))
	// 	{
	// 		if (empty($variables['iCalSettings']))
	// 		{
	//
	// 			$variables['iCalSettings'] = craft()->boxOffice_iCalSettings->getICalSettingsByPerformanceGroupId($variables['performanceGroupId']);
	//
	// 			if (!$variables['iCalSettings'])
	// 			{
	// 				$variables['iCalSettings'] = new BoxOffice_ICalSettingsModel();
	// 				$variables['brandNewICalSettings'] = true;
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		throw new HttpException(404);
	// 	}
	//
	// 	$variables['crumbs'] = array(
	// 		array('label' => Craft::t('Box Office'), 'url' => UrlHelper::getUrl('boxoffice')),
	// 		array('label' => Craft::t('Performance Groups'), 'url' => UrlHelper::getUrl('boxoffice/performance-groups')),
	// 	);
	//
	// 	$this->renderTemplate('boxoffice/performance-groups/_ical-settings', $variables);
	//
	// }
	//
	//
	// /**
	//  * Save the iCal settings into a record
	//  */
	// public function actionSaveICalSettings()
	// {
	//
	// 	$this->requirePostRequest();
	//
	// 	$iCalSettings = new BoxOffice_ICalSettingsModel();
	//
	// 	$iCalSettings->id                 = craft()->request->getPost('id');
	// 	$iCalSettings->performanceGroupId = craft()->request->getPost('performanceGroupId');
	// 	$iCalSettings->summary            = craft()->request->getPost('summary');
	// 	$iCalSettings->description        = craft()->request->getPost('description');
	// 	$iCalSettings->location           = craft()->request->getPost('location');
	// 	$iCalSettings->duration           = craft()->request->getPost('duration');
	//
	// 	// Save it
	// 	if (craft()->boxOffice_iCalSettings->saveICalSettings($iCalSettings))
	// 	{
	// 		craft()->userSession->setNotice(Craft::t('iCal Settings saved.'));
	// 		$this->redirectToPostedUrl();
	// 	}
	// 	else
	// 	{
	// 		craft()->userSession->setError(Craft::t('Couldn’t save the iCal Settings.'));
	// 	}
	//
	// 	// Send the data back to the template
	// 	craft()->urlManager->setRouteVariables(array(
	// 		'performanceGroupId' => $iCalSettings->performanceGroupId,
	// 		'summary' => $iCalSettings->summary,
	// 		'description' => $iCalSettings->description,
	// 		'location' => $iCalSettings->location,
	// 		'duration' => $iCalSettings->duration
	// 	));
	//
	// }


}
