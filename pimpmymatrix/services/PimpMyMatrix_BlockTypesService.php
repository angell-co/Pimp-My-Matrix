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

class PimpMyMatrix_BlockTypesService extends BaseApplicationComponent
{

	private $_blockTypesByContext;

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

			$blockTypes = array();

			foreach ($blockTypeRecords as $blockTypeRecord)
			{
				$blockType = $this->_populateBlockTypeFromRecord($blockTypeRecord);
				$this->_blockTypesByContext[$context][$blockType->id] = $blockType;
			}

		}
		else
		{
			return null;
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

		$blockTypeRecord = new PimpMyMatrix_BlockTypeRecord();

		$blockTypeRecord->fieldId           = $blockType->fieldId;
		$blockTypeRecord->matrixBlockTypeId = $blockType->matrixBlockTypeId;
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
				$blockTypeRecord->save(false);

				// Might as well update our cache of the block type group while we have it.
				$this->_blockTypesByContext[$blockType->context] = $blockType;

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

		// Save the MatrixBlockType model on to our model
		$blockType->matrixBlockType = $blockType->getBlockType();

		return $blockType;
	}

}
