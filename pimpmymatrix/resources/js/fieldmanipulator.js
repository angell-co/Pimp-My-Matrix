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
 * and keeps them up to date based on the current context.
 *
 * Also adds any field layouts that may exist for each block type
 * in the current context.
 */
PimpMyMatrix.FieldManipulator = Garnish.Base.extend(
{

  $matrixContainer: null,

  init: function(settings)
  {

    // Set up
    this.setSettings(settings, PimpMyMatrix.FieldManipulator.defaults);
    this.refreshMatrixContainers();

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
            this.refreshMatrixContainers();
            this.processMatrixFields();
          }
        });
        break;

      default:

    }

    // Wait until load to loop the Matrix fields
    this.addListener(Garnish.$win, 'load resize', 'processMatrixFields');

  },

  // Update our copy of all the Matrix containers
  refreshMatrixContainers: function()
  {
    this.$matrixContainer = $('.matrix').not('.widget .matrix, .superTable .matrix');
  },

  processMatrixFields: function()
  {

    var _this = this;

    // loop each matrix field
    this.$matrixContainer.each(function()
    {

      var $matrixField = $(this);

      // sort out the button groups
      _this.initBlockTypeGroups($matrixField);

      // initialize the blocks
      $matrixField.find('.blocks > .matrixblock').each(function()
      {
        _this.initBlocks($(this), $matrixField);
      });

    });

  },

  initBlockTypeGroups: function($matrixField)
  {

    // check if we’ve already pimped this field
    if ( !$matrixField.data('pimped') )
    {

      // get matrix field handle out of the dom
      var matrixFieldHandle = this._getMatrixFieldName($matrixField, true);

      // Filter by the current matrix field
      var pimpedBlockTypes = [];

      // Check current context first
      if (typeof this.settings.blockTypes[this.settings.context] !== "undefined")
      {
        pimpedBlockTypes = $.grep(this.settings.blockTypes[this.settings.context], function(e){ return e.fieldHandle === matrixFieldHandle; });
      }

      // Check global context
      if (pimpedBlockTypes.length < 1 && typeof this.settings.blockTypes['global'] !== "undefined")
      {
        pimpedBlockTypes = $.grep(this.settings.blockTypes['global'], function(e){ return e.fieldHandle === matrixFieldHandle; });
      }

      // Check we have some config
      if ( pimpedBlockTypes.length >= 1 )
      {

        // add some data to tell us we’re pimped
        $matrixField.data('pimped', true);

        // find the original buttons
        var $origButtons = $matrixField.find('> .buttons').first();

        // hide the original ones and start the button pimping process
        $origButtons.hide();

        // make our own container, not using .buttons as it gets event binds
        // from MatrixInput.js that we really don't want
        var $ourButtons = $('<div class="buttons-pimped" />').insertAfter($origButtons),
            $ourButtonsInner = $('<div class="btngroup" />').appendTo($ourButtons);

        // loop each block type config
        for (var i = 0; i < pimpedBlockTypes.length; i++)
        {

          // check if group exists, add if not
          if ( $ourButtonsInner.find('[data-pimped-group="'+pimpedBlockTypes[i]['groupName']+'"]').length === 0 )
          {
            $('<div class="btn  menubtn">'+pimpedBlockTypes[i]['groupName']+'</div><div class="menu" data-pimped-group="'+pimpedBlockTypes[i]['groupName']+'"><ul /></div>').appendTo($ourButtonsInner);
          }

          // find sub group
          $groupUl = $ourButtonsInner.find('[data-pimped-group="'+pimpedBlockTypes[i]['groupName']+'"] ul');

          // make link in new sub group
          $('<li><a data-type="'+pimpedBlockTypes[i]['matrixBlockType']['handle']+'">'+pimpedBlockTypes[i]['matrixBlockType']['name']+'</a></li>').appendTo($groupUl);

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

  },

  initBlocks: function($matrixBlock, $matrixField)
  {

    if ( !$matrixBlock.data('pimped') )
    {

      // Set this so we don’t re-run this
      $matrixBlock.data('pimped', true);

      // Get matrix field handle out of the dom
      var matrixFieldHandle = this._getMatrixFieldName($matrixField, true);

      // Filter by the current matrix field
      var pimpedBlockTypes = [];

      // Check current context first
      if (typeof this.settings.blockTypes[this.settings.context] !== "undefined")
      {
        pimpedBlockTypes = $.grep(this.settings.blockTypes[this.settings.context], function(e){ return e.fieldHandle === matrixFieldHandle; });
      }

      // Check global context
      if (pimpedBlockTypes.length < 1 && typeof this.settings.blockTypes['global'] !== "undefined")
      {
        pimpedBlockTypes = $.grep(this.settings.blockTypes['global'], function(e){ return e.fieldHandle === matrixFieldHandle; });
      }

      // Check we have some config
      if ( pimpedBlockTypes.length >= 1 )
      {

        // Get the current blocks type out of the dom
        var matrixBlockTypeHandle = this._getMatrixBlockTypeHandle($matrixBlock);

        // Further filter our pimpedBlockTypes array by the current block’s type
        var pimpedBlockType = $.grep(pimpedBlockTypes, function(e){ return e.matrixBlockType.handle === matrixBlockTypeHandle; });

        // Initialize the field layout on the block
        if ( pimpedBlockType.length === 1 && pimpedBlockType[0].fieldLayoutId !== null )
        {
          $matrixBlock.data('pimped-block-type', pimpedBlockType[0]);
          this.initBlockFieldLayout($matrixBlock, $matrixField);
        }
        // If that failed, do another check against the global context
        else
        {
          pimpedBlockTypes = $.grep(this.settings.blockTypes['global'], function(e){ return e.fieldHandle === matrixFieldHandle; });

          if ( pimpedBlockTypes.length >= 1 )
          {
            pimpedBlockType = $.grep(pimpedBlockTypes, function(e){ return e.matrixBlockType.handle === matrixBlockTypeHandle; });

            if ( pimpedBlockType.length === 1 && pimpedBlockType[0].fieldLayoutId !== null )
            {
              $matrixBlock.data('pimped-block-type', pimpedBlockType[0]);
              this.initBlockFieldLayout($matrixBlock, $matrixField);
            }
          }
        }

      }

    }

  },

  initBlockFieldLayout: function($matrixBlock, $matrixField)
  {

    var pimpedBlockType = $matrixBlock.data('pimped-block-type'),
        tabs = pimpedBlockType.fieldLayout.tabs,
        fields = pimpedBlockType.fieldLayout.fields;

    // Check we have more than one tab
    if ( tabs.length > 1 )
    {
      // Add a class so we can style
      $matrixBlock.addClass('matrixblock-pimped');

      // Get a namespaced id
      var namespace = $matrixField.prop('id') + '-' + $matrixBlock.data('id'),
          pimpedNamespace = 'pimpmymatrix-' + namespace;

      // Add the tabs container
      var $tabs = $('<ul class="pimpmymatrix-tabs"/>').appendTo($matrixBlock);

      // Make our own fields container and hide the native one
      var $pimpedFields = $('<div class="pimpmymatrix-fields"/>').appendTo($matrixBlock),
          $fields = $matrixBlock.find('.fields');
      $fields.hide();

      // Loop the tabs
      for (var i = 0; i < tabs.length; i++)
      {

        // Set up the first one to be active
        var navClasses = '',
            paneClasses = '';

        if (i==0)
        {
          navClasses = ' sel';
        }
        else
        {
          paneClasses = ' hidden';
        }

        // Add the tab nav
        var $tabLi = $('<li/>').appendTo($tabs);
        $('<a id="'+pimpedNamespace+'-'+i+'" class="tab'+navClasses+'">'+tabs[i].name+'</a>')
          .appendTo($tabLi)
          .data('pimped-tab-target', '#'+pimpedNamespace+'-pane-'+i);

        // Make a tab pane
        var $pane = $('<div id="'+pimpedNamespace+'-pane-'+i+'" class="'+paneClasses+'"/>').appendTo($pimpedFields);

        // Filter the fields array by their associated tabId and loop over them
        var tabFields = $.grep(fields, function(e){ return e.tabId === tabs[i].id; });
        for (var n = 0; n < tabFields.length; n++)
        {
          // Move the required field to our new container
          $fields.find('#' + namespace + '-fields-' + tabFields[n].field.handle + '-field').appendTo($pane);
        }

      }

      // Add the event handlers
      this.addListener($tabs.find('a'), 'click', 'onTabClick');

    }

  },

  onTabClick: function(ev)
  {

    ev.preventDefault();
    ev.stopPropagation();

    var $tab = $(ev.target),
        $tabNav = $tab.parent().parent('.pimpmymatrix-tabs'),
        targetSelector = $tab.data('pimped-tab-target'),
        $target = $(targetSelector);

    // Toggle tab nav state
    $tabNav.find('a.sel').removeClass('sel');
    $tab.addClass('sel');

    // Toggle the pane state
    $target.siblings('div').addClass('hidden');
    $target.removeClass('hidden');

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
  },

  /**
   * Returns the block type handle for a given $matrixBlock
   */
  _getMatrixBlockTypeHandle: function($matrixBlock)
  {
    var blockTypeHandle = $matrixBlock.find('input[type="hidden"][name*="type"]').val();

    if ( typeof blockTypeHandle == 'string' )
    {
      return blockTypeHandle;
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
