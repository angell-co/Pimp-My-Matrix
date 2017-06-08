<?php
namespace Craft;

/**
 * Pimp My Matrix by Supercool
 *
 * @package   PimpMyMatrix
 * @author    Josh Angell
 * @copyright Copyright (c) 2015, Supercool Ltd
 * @link      http://www.supercooldesign.co.uk
 */

class PimpMyMatrix_BlockTypesService extends BaseApplicationComponent
{

	private $_blockTypesByContext;

	/**
	 * Returns a single PimpMyMatrix_BlockTypeModel
	 *
	 * @method getBlockType
	 * @param  string       $context           required
	 * @param  int          $matrixBlockTypeId required
	 * @return bool|PimpMyMatrix_BlockTypeModel
	 */
	public function getBlockType($context = false, $matrixBlockTypeId = false)
	{

		if (!$context || !$matrixBlockTypeId)
		{
			return false;
		}

		$blockTypeRecord = PimpMyMatrix_BlockTypeRecord::model()->findByAttributes(array(
			'context'           => $context,
			'matrixBlockTypeId' => $matrixBlockTypeId
		));

		return $this->_populateBlockTypeFromRecord($blockTypeRecord);

	}

	/**
	 * Returns a block type by its context.
	 *
	 * @param $context
	 * @param $groupBy          Group by an optional model attribute to group by
	 * @param $ignoreSubContext Optionally ignore the sub context (id)
	 * @return array
	 */
	public function getBlockTypesByContext($context, $groupBy = false, $ignoreSubContext = false, $fieldId = false)
	{

		if ($ignoreSubContext)
		{
			$blockTypeRecords = PimpMyMatrix_BlockTypeRecord::model()->findAll(array(
				'condition' => $fieldId ? "fieldId = '{$fieldId}' AND context LIKE '{$context}%'" : "context LIKE '{$context}%'"
			));
		}
		else
		{
			$attributes = array('context' => $context);

			if ($fieldId)
			{
				$attributes['fieldId'] = $fieldId;
			}

			$blockTypeRecords = PimpMyMatrix_BlockTypeRecord::model()->findAllByAttributes($attributes);
		}

		if ($blockTypeRecords)
		{

			foreach ($blockTypeRecords as $blockTypeRecord)
			{
				$blockType = $this->_populateBlockTypeFromRecord($blockTypeRecord);
				$this->_blockTypesByContext[$context][$blockType->id] = $blockType;
			}

		}
		else
		{
			return array();
		}

		if ($groupBy)
		{
			$return = array();

			foreach ($this->_blockTypesByContext[$context] as $blockType)
			{
				$return[$blockType->$groupBy][] = $blockType;
			}
			return $return;
		}
		else
		{
			return $this->_blockTypesByContext[$context];
		}

	}

	/**
	 * Saves our version of a block type
	 *
	 * @method saveBlockType
	 * @param  PimpMyMatrix_BlockTypeModel $blockType
	 * @throws \Exception
	 * @return bool
	 */
	public function saveBlockType(PimpMyMatrix_BlockTypeModel $blockType)
	{

		if ($blockType->id)
		{
			$blockTypeRecord = PimpMyMatrix_BlockTypeRecord::model()->findById($blockType->id);

			if (!$blockTypeRecord)
			{
				throw new Exception(Craft::t('No PimpMyMatrix block type exists with the ID “{id}”', array('id' => $blockType->id)));
			}
		}
		else
		{
			$blockTypeRecord = new PimpMyMatrix_BlockTypeRecord();
		}

		$blockTypeRecord->fieldId           = $blockType->fieldId;
		$blockTypeRecord->matrixBlockTypeId = $blockType->matrixBlockTypeId;
		$blockTypeRecord->fieldLayoutId     = $blockType->fieldLayoutId;
		$blockTypeRecord->groupName         = $blockType->groupName;
		$blockTypeRecord->context           = $blockType->context;

		$blockTypeRecord->validate();
		$blockType->addErrors($blockTypeRecord->getErrors());

		if (!$blockType->hasErrors())
		{
			$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
			try
			{

				// Save it!
				$isNew = $blockTypeRecord->isNewRecord();
				$blockTypeRecord->save(false);
				if ($isNew) {
					$blockType->id = $blockTypeRecord->id;
				}

				// Might as well update our cache of the block type group while we have it.
				$this->_blockTypesByContext[$blockType->context][$blockType->id] = $blockType;

				if ($transaction !== null)
				{
					$transaction->commit();
				}
			}
			catch (\Exception $e)
			{
				if ($transaction !== null)
				{
					$transaction->rollback();
				}

				throw $e;
			}

			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Deletes all the block types for a given context
	 *
	 * @param string $context
	 * @throws \Exception
	 * @return bool
	 */
	public function deleteBlockTypesByContext($context = false, $fieldId = false)
	{

		if (!$context)
		{
			return false;
		}

		$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
		try
		{

			$attributes = array('context' => $context);

			if ($fieldId)
			{
				$attributes['fieldId'] = $fieldId;
			}

			$affectedRows = craft()->db->createCommand()->delete('pimpmymatrix_blocktypes', $attributes);

			if ($transaction !== null)
			{
				$transaction->commit();
			}

			// update the internal cache
			$this->_blockTypesByContext[$context] = [];

			return (bool) $affectedRows;
		}
		catch (\Exception $e)
		{
			if ($transaction !== null)
			{
				$transaction->rollback();
			}

			throw $e;
		}

	}


	/**
	 * Saves a field layout and attaches it to the given pimped block type
	 *
	 * @param  PimpMyMatrix_BlockTypeModel $pimpedBlockType [description]
	 * @return bool
	 */
	public function saveFieldLayout(PimpMyMatrix_BlockTypeModel $pimpedBlockType)
	{

		// First, get the layout and save the old field layout id for later
		$layout = $pimpedBlockType->getFieldLayout();
		$oldFieldLayoutId = $pimpedBlockType->fieldLayoutId;

		// Second save the layout - replicated from FieldsService::saveLayout()
		// to allow us to retain the $layout->id for our own use
		if ($layout->getTabs())
		{
			$layoutRecord = new FieldLayoutRecord();
			$layoutRecord->type = 'PimpMyMatrix_BlockType';
			$layoutRecord->save(false);
			$layout->id = $layoutRecord->id;

			foreach ($layout->getTabs() as $tab)
			{
				$tabRecord = new FieldLayoutTabRecord();
				$tabRecord->layoutId  = $layout->id;
				$tabRecord->name      = $tab->name;
				$tabRecord->sortOrder = $tab->sortOrder;
				$tabRecord->save(false);
				$tab->id = $tabRecord->id;

				foreach ($tab->getFields() as $field)
				{
					$fieldRecord = new FieldLayoutFieldRecord();
					$fieldRecord->layoutId  = $layout->id;
					$fieldRecord->tabId     = $tab->id;
					$fieldRecord->fieldId   = $field->fieldId;
					$fieldRecord->required  = $field->required;
					$fieldRecord->sortOrder = $field->sortOrder;
					$fieldRecord->save(false);
					$field->id = $fieldRecord->id;
				}
			}

			// Now we have saved the layout, update the id on the given
			// pimped blocktype model
			$pimpedBlockType->fieldLayoutId = $layout->id;

		}
		else
		{
			$pimpedBlockType->fieldLayoutId = null;
		}

		// Save our pimped block type again
		if ($this->saveBlockType($pimpedBlockType))
		{
			// Delete the old field layout
			craft()->fields->deleteLayoutById($oldFieldLayoutId);
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Returns an array of fieldLayoutIds indexed by matrixBlockTypeIds
	 * for the given context and fieldId combination
	 *
	 * @param  string            $context required
	 * @param  int               $fieldId required
	 * @return false|array
	 */
	public function getFieldLayoutIds($context, $fieldId = false)
	{

		if (!$fieldId)
		{
			return false;
		}

		$blockTypeRecords = PimpMyMatrix_BlockTypeRecord::model()->findAllByAttributes(array(
			'context' => $context,
			'fieldId' => $fieldId
		));

		$return = array();
		foreach ($blockTypeRecords as $blockTypeRecord)
		{
			$return[$blockTypeRecord->matrixBlockTypeId] = $blockTypeRecord->fieldLayoutId;
		}
		return $return;

	}


	// Private Methods
	// =========================================================================

	/**
	 * Populates a PimpMyMatrix_BlockTypeModel with attributes from a PimpMyMatrix_BlockTypeRecord.
	 *
	 * @param PimpMyMatrix_BlockTypeRecord|null
	 *
	 * @return PimpMyMatrix_BlockTypeModel|null
	 */
	private function _populateBlockTypeFromRecord($blockTypeRecord)
	{
		if (!$blockTypeRecord)
		{
			return null;
		}

		$blockType = PimpMyMatrix_BlockTypeModel::populateModel($blockTypeRecord);

		// Use the fieldId to get the field and save the handle on to the model
		$matrixField = craft()->fields->getFieldById($blockType->fieldId);
		$blockType->fieldHandle = $matrixField->handle;

		// Save the MatrixBlockTypeModel on to our model
		$blockType->matrixBlockType = $blockType->getBlockType();

		// Save the field layout content on to our model
		$layout = $blockType->getFieldLayout();
		$fields = array();
		foreach ($layout->getFields() as $field)
		{
			$fields[] = array(
				'tabId' => $field->tabId,
				'sortOrder' => $field->sortOrder,
				'field' => $field->getField()
			);
		}

		$blockType->fieldLayout = array(
			'tabs'   => $layout->getTabs(),
			'fields' => $fields
		);

		return $blockType;
	}

}
