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
 * Overrides the default Matrix ‘add block’ buttons with our grouped ones
 * and keeps them up to date based on the current context
 */
PimpMyMatrix.BlockTypeGrouper = Garnish.Base.extend(
{

  currentBlockTypeGroups: false,

  $matrixContainer: null,

  init: function(settings)
  {

    // Set up
    this.setSettings(settings, PimpMyMatrix.BlockTypeGrouper.defaults);
    this.refreshCurrentBlockTypeGroups();

    // Work out what kind of context we’re working with
    // so we can keep things up to date
    switch (this.settings.context.split(':')[0])
    {

      case 'entrytype':
        // Thanks mmikkel: http://craftcms.stackexchange.com/a/9466/144
        this.addListener(Garnish.$doc, 'ajaxComplete', function(ev, status, requestData)
        {
          if ( requestData.url.indexOf( 'switchEntryType' ) > -1 )
          {
            this.settings.context = 'entrytype:' + $('#entryType').val();
            this.refreshCurrentBlockTypeGroups();
            this.loopMatrixFields();
          }
        });
        break;

      default:

    }

    // Wait until load to loop the Matrix fields
    this.addListener(Garnish.$win, 'load', 'loopMatrixFields');

  },

  refreshCurrentBlockTypeGroups: function()
  {
    this.$matrixContainer = $('.matrix').not('.widget .matrix, .superTable .matrix');
    this.currentBlockTypeGroups = this.settings.blockTypes[this.settings.context];
  },

  loopMatrixFields: function()
  {

    var _this = this;

    // loop each matrix field
    this.$matrixContainer.each(function()
    {
      // sort buttons
      _this.sortButtons($(this));
    });

  },

  sortButtons: function($matrixField)
  {

    // get matrix field handle out of DOM
    var matrixFieldHandle = this._getMatrixFieldName($matrixField, true);

    // check we have some block type groups
    if ( this.currentBlockTypeGroups )
    {
      // Filter by the current matrix field
      var blockTypeGroups = $.grep(this.currentBlockTypeGroups, function(e){ return e.fieldHandle === matrixFieldHandle; });

      // Check we have some config
      if ( typeof blockTypeGroups !== "undefined" && blockTypeGroups.length > 1 )
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

          // loop each block type group
          for (var i = 0; i < blockTypeGroups.length; i++)
          {

            // check if group exists, add if not
            if ( $ourButtonsInner.find('[data-pimped-group="'+blockTypeGroups[i]['groupName']+'"]').length === 0 )
            {
              $('<div class="btn  menubtn">'+blockTypeGroups[i]['groupName']+'</div><div class="menu" data-pimped-group="'+blockTypeGroups[i]['groupName']+'"><ul /></div>').appendTo($ourButtonsInner);
            }

            // find sub group
            $groupUl = $ourButtonsInner.find('[data-pimped-group="'+blockTypeGroups[i]['groupName']+'"] ul');

            // make link in new sub group
            $('<li><a data-type="'+blockTypeGroups[i]['matrixBlockType']['handle']+'">'+blockTypeGroups[i]['matrixBlockType']['name']+'</a></li>').appendTo($groupUl);

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
},
{
  defaults: {
    blockTypes: null,
    context: false
  }
});


})(jQuery);
