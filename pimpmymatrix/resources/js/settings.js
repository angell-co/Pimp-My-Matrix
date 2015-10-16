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


PimpMyMatrix.Configurator = Garnish.Base.extend(
{

  $container: null,
  fields: [],

  init: function(container, settings)
  {

    this.$container = $(container);
    this.setSettings(settings, PimpMyMatrix.Configurator.defaults);

    this.$container.on('mousedown', this.settings.fieldSelector, $.proxy(this.modifySettingsButtons, this));

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
               .text(Craft.t('Pimp'))
              //  .data('_reasonsField', $field)
               .attr('data-action', 'pimp')
               .on('click', $.proxy(_this.onFieldConfiguratorClick, _this));

        $field.data('pimpmymatrix-configurator-initialized', true);
      }

    });

  },

  onFieldConfiguratorClick: function(ev)
  {

    var $form = $('<form class="modal elementselectormodal pimpmymatrix__configurator"/>'),
        $body = $('<div class="body"/>').appendTo($form),
        $body = $('<div class="content"/>').appendTo($body),
        $spinner = $('<div class="spinner big"/>').appendTo($body),
        $body = $('<div class="main"/>').appendTo($body);

        // Get the html
		// Craft.postActionRequest(this.controllerAction, { ids : this.elementIds }, $.proxy(function(response, textStatus)
		// {
		// 	if (textStatus == 'success')
		// 	{
		// 		// Update the form
		// 		this._updateForm(response);
		// 	}
		// }, this));

    var $footer = $('<div class="footer"/>').appendTo($form),
        $buttons = $('<div class="buttons right"/>').appendTo($footer),
        $cancelBtn = $('<div class="btn">'+Craft.t('Cancel')+'</div>').appendTo($buttons),
        $submitBtn = $('<input type="submit" class="btn submit disabled" disabled value="'+Craft.t('Save')+'"/>').appendTo($buttons),
        modal = new Garnish.Modal($form,
        {
          resizable: true,
          closeOtherModals: true
        });

    modal.on('show', $.proxy(function()
    {
      console.log('loading');
    }, this));

    modal.on('hide', $.proxy(function()
    {
      modal.$container.remove();
      modal.$shade.remove();
      delete modal;
    }, this));

    this.addListener($form, 'submit', '_handleSubmit');
    this.addListener($cancelBtn, 'click', function()
    {
      modal.hide()
    });

  },

  _handleSubmit: function()
  {
    console.log('submitted');
  }

},
{
  defaults: {
    matrixFieldIds: null,
    fieldSelector: '.fld-tabs .fld-field'
  }
});


})(jQuery);
