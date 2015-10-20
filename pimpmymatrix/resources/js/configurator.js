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


/**
 * Adds itself to the settings menu of and Matrix field in a fld
 * and provides a fld interface for the configuration of the block
 * type groups and a further fld for each block type’s fields.
 */
PimpMyMatrix.Configurator = Garnish.Base.extend(
{

  $container: null,
  fields: [],

  $form: null,
  $spinner: null,

  init: function(container, settings)
  {

    this.$container = $(container);
    this.setSettings(settings, PimpMyMatrix.Configurator.defaults);

    setTimeout($.proxy(this.modifySettingsButtons,this),0);
    this.$container.on('mousedown', this.settings.fieldSelector, $.proxy(this.onFieldMouseDown, this));

  },

  onFieldMouseDown: function(ev)
  {
    ev.preventDefault();
    ev.stopPropagation();
    this.modifySettingsButtons();
  },

  modifySettingsButtons: function()
  {

    var _this = this;

    // Work out which fields are Matrix fields
    this.fields = [];
    this.$container.find(_this.settings.fieldSelector).each(function()
    {
      var id = $(this).data('id').toString();
      if ($.inArray(id, _this.settings.matrixFieldIds) !== -1)
      {
        _this.fields.push($(this));
      }
    });

    // Loop over the settings buttons
    $.each(this.fields, function()
    {

      var $field = $(this);

      if (!$field.data('pimpmymatrix-configurator-initialized'))
      {

        var menuBtn = $field.find('a.settings').data('menubtn') || false;

        if (!menuBtn)
        {
          return;
        }

        var $menu = menuBtn.menu.$container

        $menu.find('ul')
             .children(':first')
             .clone(true)
             .prependTo($menu.find('ul:first'))
             .find('a:first')
               .text(Craft.t('Group block types'))
               .data('pimpmymatrix-field-id', $field.data('id'))
               .attr('data-action', 'pimp')
               .on('click', $.proxy(_this.onFieldConfiguratorClick, _this));

        $field.data('pimpmymatrix-configurator-initialized', true);

      }

    });

  },

  onFieldConfiguratorClick: function(ev)
  {

    ev.preventDefault();
    ev.stopPropagation();

    var fieldId = $(ev.target).data('pimpmymatrix-field-id');

    this.$form = $('<form class="modal elementselectormodal pimpmymatrix-configurator"/>');

    var $body = $('<div class="body"/>').appendTo(this.$form),
        $body = $('<div class="content"/>').appendTo($body),
        $bigSpinner = $('<div class="spinner big"/>').appendTo($body),
        $body = $('<div class="main"/>').appendTo($body),
        $footer = $('<div class="footer"/>').appendTo(this.$form),
        $buttons = $('<div class="buttons right"/>').appendTo($footer);

    this.$spinner = $('<div class="spinner hidden"/>').appendTo($buttons);

    var $cancelBtn = $('<div class="btn">'+Craft.t('Cancel')+'</div>').appendTo($buttons),
        $submitBtn = $('<input type="submit" class="btn submit" value="'+Craft.t('Save')+'"/>').appendTo($buttons),
        _this = this,
        modal = new Garnish.Modal(this.$form,
        {
          resizable: true,
          closeOtherModals: true,
          onFadeIn: function()
          {
            // Load a fld with all the blocks in the un-used section, this will
            // allow grouping them in 'tabs' to give us the block groups like normal
            var data = {
              fieldId : fieldId,
              context : _this.settings.context
            };
            Craft.postActionRequest('pimpMyMatrix/getConfigurator', data, $.proxy(function(response, textStatus)
            {
              if (textStatus == 'success')
              {
                $(response.html).appendTo($body);
                $bigSpinner.addClass('hidden');
                var fld = new PimpMyMatrix.FieldLayoutDesigner('#pimpmymatrix-configurator', {
                  fieldInputName: 'pimpedBlockTypes[__TAB_NAME__][]'
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

    // Add the context
    data.push({
      'name' : 'context',
      'value' : this.settings.context
    });

    // Post it
    Craft.postActionRequest('pimpMyMatrix/blockTypes/saveBlockTypes', data, $.proxy(function(response, textStatus)
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

},
{
  defaults: {
    matrixFieldIds: null,
    context: false,
    fieldSelector: '.fld-tabcontent > .fld-field'
  }
});


})(jQuery);
