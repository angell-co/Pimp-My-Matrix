/**
 * @author    Supercool Ltd <josh@supercooldesign.co.uk>
 * @copyright Copyright (c) 2014, Supercool Ltd
 * @see       http://supercooldesign.co.uk
 */

(function($){


if (typeof PimpMyMatrix == 'undefined')
{
  PimpMyMatrix = {};
}


PimpMyMatrix.FieldLayoutDesigner = Craft.FieldLayoutDesigner.extend(
{

  initField: function($field)
  {
    var $editBtn = $field.find('.settings'),
        $menu = $('<div class="menu" data-align="center"/>').insertAfter($editBtn),
        $ul = $('<ul/>').appendTo($menu);

    $('<li><a data-action="pimp-again">'+Craft.t('Edit field layout')+'</a></li>').appendTo($ul);

    $('<li><a data-action="remove">'+Craft.t('Remove')+'</a></li>').appendTo($ul);

    new Garnish.MenuBtn($editBtn, {
      onOptionSelect: $.proxy(this, 'onFieldOptionSelect')
    });
  },

  onFieldOptionSelect: function(option)
  {
    var $option = $(option),
        $field = $option.data('menu').$trigger.parent(),
        action = $option.data('action');

    switch (action)
    {
      case 'pimp-again':
      {
        // TODO: This should pop open another modal with another fld in there to enable
        // fields and tabs to happen inside the block
        alert("$$$ PIMPING BRAH $$$");
        // this.toggleRequiredField($field, $option);
        break;
      }
      case 'remove':
      {
        this.removeField($field);
        break;
      }
    }
  }

});


})(jQuery);
