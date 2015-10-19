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

class PimpMyMatrix_BlockTypeGroupModel extends BaseModel
{

	// Public Methods
	// =========================================================================

	public function __toString()
	{
		return Craft::t($this->id);
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
			'matrixBlockTypeId' => AttributeType::Number,
			'tabName'           => AttributeType::String,
			'context'           => AttributeType::String,
		);
	}

}
