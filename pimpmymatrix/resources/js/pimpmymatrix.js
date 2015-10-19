/**
 * @author    Supercool Ltd <josh@supercooldesign.co.uk>
 * @copyright Copyright (c) 2014, Supercool Ltd
 * @see       http://supercooldesign.co.uk
 */

(function($){

/**
 * PimpMyMatrix Class
 */
Craft.PimpMyMatrix = Garnish.Base.extend(
{

  buttonConfig: null,

  $matrixContainer: null,

  init: function(buttonConfig)
  {
    this.$matrixContainer = $('.matrix').not('.widget .matrix, .superTable .matrix');

    this.buttonConfig = buttonConfig;

    this.addListener(Garnish.$win, 'load', 'loopMatrixFields');
  },

  loopMatrixFields: function()
  {

    var that = this;

    // loop each matrix field
    this.$matrixContainer.each($.proxy(function()
    {

      // sort buttons
      that.sortButtons($(this));

    }), this);

  },

  sortButtons: function($matrixField)
  {

    // get matrix field handle out of DOM
    var matrixFieldHandle = this._getMatrixFieldName($matrixField, true);

    // look for an object that matches this field in the config array
    if ( typeof this.buttonConfig !== "undefined" )
    {
      var buttonConfig = $.grep(this.buttonConfig, function(e){ return e.fieldHandle === matrixFieldHandle; });

      // if we found one (and it has at least one group)
      if ( typeof buttonConfig[0] !== "undefined" )
      {

        // find the original buttons
        var $origButtons = $matrixField.find('> .buttons').first();

        // from there, check if we've already pimped those buttons
        if ( $origButtons.next('.buttons-pimped').length < 1 )
        {

          // if we haven't already pimped them, hide the original ones and start the button pimping process
          $origButtons.hide();

          // make our own container, not using .buttons as it gets event binds
          // from MatrixInput.js that we really don't want
          var $ourButtons = $('<div class="buttons-pimped" />').insertAfter($origButtons),
              $ourButtonsInner = $('<div class="btngroup" />').appendTo($ourButtons);

          // loop each blockType / group pairing
          var buttonObject = buttonConfig[0].config;
          for (var key in buttonObject)
          {

            // check if group exists, add if not
            if ( $ourButtonsInner.find('[data-pimped-group="'+buttonObject[key]['group']+'"]').length === 0 )
            {
              $('<div class="btn  menubtn">'+buttonObject[key]['group']+'</div><div class="menu" data-pimped-group="'+buttonObject[key]['group']+'"><ul /></div>').appendTo($ourButtonsInner);
            }

            // find sub group
            $groupUl = $ourButtonsInner.find('[data-pimped-group="'+buttonObject[key]['group']+'"] ul');

            // make link in new sub group
            $('<li><a data-type="'+buttonObject[key]['blockType']['handle']+'">'+buttonObject[key]['blockType']['name']+'</a></li>').appendTo($groupUl);

          }

          // make triggers MenuBtns
          $ourButtonsInner.find('.menubtn').each(function()
          {

            new Garnish.MenuBtn($(this),
            {
              onOptionSelect: function(option)
              {
                // find our type and click the correct original btn!
                var type = $(option).data('type');
                $origButtons.find('[data-type="'+type+'"]').trigger('click');
              }
            });

          });

        }
      }
    }

  },

  /**
   * This simply returns a fieldHandle if it can get one or false if not
   */
  _getMatrixFieldName: function($matrixField, fromId)
  {
    if ( fromId )
    {
      var matrixFieldId = $matrixField.parents('.field').prop('id'),
          parts = matrixFieldId.split("-"),
          matrixFieldHandle = parts[1];
    }
    else
    {
      var matrixFieldName = $matrixField.siblings('input[type="hidden"][name*="fields"]').prop('name'),
          regExp  = /fields\[([^\]]+)\]/,
          matches = regExp.exec(matrixFieldName),
          matrixFieldHandle = matches[1];
    }

    if ( matrixFieldHandle != '' )
    {
      return matrixFieldHandle;
    }
    else
    {
      return false;
    }
  }

});


})(jQuery);
