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

class PimpMyMatrix_BlockTypeModel extends BaseModel
{

	// Public Methods
	// =========================================================================

	public function __toString()
	{
		return Craft::t($this->getBlockType()->name);
	}

	public function getBlockType()
	{
		if ( $this->matrixBlockType )
		{
			return $this->matrixBlockType;
		}
		else
		{
			return craft()->matrix->getBlockTypeById($this->matrixBlockTypeId);
		}
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'fieldLayout' => new FieldLayoutBehavior(),
		);
	}

	/**
	 * @inheritDoc BaseElementModel::getFieldLayout()
	 *
	 * @return FieldLayoutModel|null
	 */
	public function getFieldLayout()
	{
		return $this->asa('fieldLayout')->getFieldLayout();
	}

	// Protected Methods
	// =========================================================================

	/**
	 * Defines this model's attributes.
	 *
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'id'                => AttributeType::Number,
			'fieldId'           => AttributeType::Number,
			'fieldHandle'       => AttributeType::String,
			'matrixBlockTypeId' => AttributeType::Number,
			'matrixBlockType'   => array(AttributeType::Mixed, 'default' => false),
			'groupName'         => AttributeType::Name,
			'context'           => AttributeType::String,
			'fieldLayoutId'     => AttributeType::Number,
			'fieldLayout'       => array(AttributeType::Mixed, 'default' => false),
		);
	}

}
