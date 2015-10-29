/**
 * @author    Supercool Ltd <josh@supercooldesign.co.uk>
 * @copyright Copyright (c) 2015, Supercool Ltd
 * @see       http://supercooldesign.co.uk
 */

(function($){


if (typeof PimpMyMatrix == 'undefined')
{
  PimpMyMatrix = {};
}


PimpMyMatrix.BlockTypeFieldLayoutDesigner = Craft.FieldLayoutDesigner.extend(
{

	initField: function($blockType)
	{
		var $editBtn = $blockType.find('.settings'),
				$menu = $('<div class="menu" data-align="center"/>').insertAfter($editBtn),
				$ul = $('<ul/>').appendTo($menu);

		$('<li><a data-action="remove">'+Craft.t('Remove')+'</a></li>').appendTo($ul);

		new Garnish.MenuBtn($editBtn, {
			onOptionSelect: $.proxy(this, 'onFieldOptionSelect')
		});
	}

});


})(jQuery);
