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


PimpMyMatrix.GroupsDesigner = Craft.FieldLayoutDesigner.extend(
{

  $form: null,
  $spinner: null,

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
    this.$form = $('<form class="modal elementselectormodal pimpmymatrix-fields-configurator"/>');

    var $body = $('<div class="body"/>').appendTo(this.$form),
        $body = $('<div class="content"/>').appendTo($body),
        $bigSpinner = $('<div class="spinner big"/>').appendTo($body),
        $body = $('<div class="main"/>').appendTo($body),
        $footer = $('<div class="footer"/>').appendTo(this.$form),
        $buttons = $('<div class="buttons right"/>').appendTo($footer);

    this.$spinner = $('<div class="spinner hidden"/>').appendTo($buttons);

    var $cancelBtn = $('<div class="btn">'+Craft.t('Cancel')+'</div>').appendTo($buttons),
        $submitBtn = $('<input type="submit" class="btn submit" value="'+Craft.t('Save')+'"/>').appendTo($buttons),
        modal = new Garnish.Modal(this.$form,
        {
          resizable: true,
          closeOtherModals: false,
          onFadeIn: $.proxy(function()
          {

            var data = {
              context : this.settings.context,
              blockTypeId : $blockType.data('id')
            };

            Craft.postActionRequest('pimpMyMatrix/getFieldsConfigurator', data, $.proxy(function(response, textStatus)
            {
              if (textStatus == 'success')
              {
                $(response.html).appendTo($body);
                $bigSpinner.addClass('hidden');
                var fld = new PimpMyMatrix.BlockTypeFieldLayoutDesigner('#pimpmymatrix-fields-configurator', {
                  fieldInputName: 'blockTypeFieldLayouts[__TAB_NAME__][]'
                });
              }
            }, this));

          }, this),
          onHide: function()
          {
            modal.$container.remove();
            modal.$shade.remove();
            delete modal;
          }
        });

    this.addListener(this.$form, 'submit', '_handleSubmit');
    this.addListener($cancelBtn, 'click', function()
    {
      modal.hide()
    });
  },

  _handleSubmit: function(ev)
  {
    ev.preventDefault();

    // Show spinner
    this.$spinner.removeClass('hidden');

    // Get the form data
    var data = this.$form.serializeArray();

    // Post it
    Craft.postActionRequest('pimpMyMatrix/blockTypes/saveFieldLayout', data, $.proxy(function(response, textStatus)
    {
      this.$spinner.addClass('hidden');
      if (textStatus == 'success' && response.success)
      {
        Craft.cp.displayNotice(Craft.t('Block type groups saved.'));
      }
      else
      {
        if (textStatus == 'success')
        {
          Craft.cp.displayError(Craft.t('An unknown error occurred.'));
        }
      }
    }, this));
  }

});


})(jQuery);
