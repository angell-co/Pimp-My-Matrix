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

  initField: function($blockType)
  {
    var $editBtn = $blockType.find('.settings'),
        $menu = $('<div class="menu" data-align="center"/>').insertAfter($editBtn),
        $ul = $('<ul/>').appendTo($menu);

    $('<li><a data-action="edit-field-layout">'+Craft.t('Edit field layout')+'</a></li>').appendTo($ul);

    $('<li><a data-action="remove">'+Craft.t('Remove')+'</a></li>').appendTo($ul);

    new Garnish.MenuBtn($editBtn, {
      onOptionSelect: $.proxy(this, 'onFieldOptionSelect')
    });
  },

  onFieldOptionSelect: function(option)
  {
    var $option = $(option),
        $blockType = $option.data('menu').$trigger.parent(),
        action = $option.data('action');

    switch (action)
    {
      case 'edit-field-layout':
      {
        this.editFieldLayout($blockType);
        break;
      }
      case 'remove':
      {
        this.removeField($blockType);
        break;
      }
    }
  },

  editFieldLayout: function($blockType)
  {
    // This pops open another modal with another fld in it to enable
    // the editing of fields and tabs to happen inside the block type
    var $form = $('<form class="modal elementselectormodal pimpmymatrix-fields-configurator"/>'),
        $body = $('<div class="body"/>').appendTo($form),
        $body = $('<div class="content"/>').appendTo($body),
        $bigSpinner = $('<div class="spinner big"/>').appendTo($body),
        $body = $('<div class="main"/>').appendTo($body),
        $footer = $('<div class="footer"/>').appendTo($form),
        $buttons = $('<div class="buttons right"/>').appendTo($footer),
        $spinner = $('<div class="spinner hidden"/>').appendTo($buttons),
        $cancelBtn = $('<div class="btn">'+Craft.t('Cancel')+'</div>').appendTo($buttons),
        $submitBtn = $('<input type="submit" class="btn submit" value="'+Craft.t('Save')+'"/>').appendTo($buttons),
        _this = this,
        modal = new Garnish.Modal($form,
        {
          resizable: true,
          closeOtherModals: false,
          onFadeIn: function()
          {

            var data = {
              blockTypeId : $blockType.data('id')
            };

            Craft.postActionRequest('pimpMyMatrix/getFieldsConfigurator', data, $.proxy(function(response, textStatus)
            {
              if (textStatus == 'success')
              {
                $(response.html).appendTo($body);
                $bigSpinner.addClass('hidden');
                var fld = new Craft.FieldLayoutDesigner('#pimpmymatrix-fields-configurator', {
                  fieldInputName: 'blockTypeFieldLayout[__TAB_NAME__][]'
                });
              }
            }, this));

          },
          onHide: function()
          {
            modal.$container.remove();
            modal.$shade.remove();
            delete modal;
          }
        });

    this.addListener($form, 'submit', '_handleSubmit');
    this.addListener($cancelBtn, 'click', function()
    {
      modal.hide()
    });
  }

});


})(jQuery);
