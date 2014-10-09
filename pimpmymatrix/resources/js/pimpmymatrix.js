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
    this.$matrixContainer = $('.matrix');

    this.buttonConfig = buttonConfig;

    this.addListener(Garnish.$win, 'load', 'loopMatrixFields');
  },

  loopMatrixFields: function()
  {

    var that = this;

    // loop each matrix field
    this.$matrixContainer.each($.proxy(function()
    {

      // sort block headings
      that.getFieldBlockTypes($(this));

      // sort buttons
      that.sortButtons($(this));

    }), this);

  },

  getFieldBlockTypes: function($matrixField)
  {
    // get matrix field handle out of DOM
    var matrixFieldHandle = this._getMatrixFieldName($matrixField);

    var that = this;

    // get array of blockTypes
    Craft.postActionRequest('pimpMyMatrix/getBlockTypesFromField', { matrixFieldHandle : matrixFieldHandle }, $.proxy(function(response, textStatus)
    {
      if (textStatus === 'success')
      {
        if (response.success)
        {

          // we have blockTypes so add them to the data object on the field
          $matrixField.data('blockTypes', response.blockTypes);

          // bind resize now on the matrix field
          that.addListener($matrixField, 'resize', 'addBlockHeadings');

          // ping addBlockHeadings anyway
          that.addBlockHeadings();

        }
      }
    }), this);

  },

  addBlockHeadings: function()
  {

    // loop available matrix fields
    this.$matrixContainer.each($.proxy(function()
    {

      // get field and blockTypes
      var $matrixField = $(this),
          blockTypes = $matrixField.data('blockTypes');

      // loop blocks if we have blockTypes
      if (typeof blockTypes !== "undefined") {
        $matrixField.find('.matrixblock').each($.proxy(function()
        {

          // cache block
          var $block = $(this);

          // final check that we haven't already added one in case something has gone mental
          if ( ! $block.hasClass('pimped') )
          {

            // get the block type handle
            var blockTypeHandle = $block.find('input[type="hidden"][name*="[type]"]').val();

            // using the blockTypes, match the handle to the blockType object
            var result = $.grep(blockTypes, function(e){ return e.handle === blockTypeHandle; });

            // check we have something
            if (result.length > 0)
            {

              // get the name and add it!
              var blockName = result[0].name;
              $block.addClass('pimped');
              $block.prepend('<div class="pimpmymatrix-heading">'+blockName+'</div>')

            }

          }

        }), this);
      }

    }), this);

  },

  sortButtons: function($matrixField)
  {

    // get matrix field handle out of DOM
    var matrixFieldHandle = this._getMatrixFieldName($matrixField);

    // look for an object that matches this field in the config array
    var buttonConfig = $.grep(this.buttonConfig, function(e){ return e.fieldHandle === matrixFieldHandle; });

    // if we found one, execute our magic
    if ( buttonConfig[0] !== undefined )
    {

      // find and hide the original buttons
      var $origButtons = $matrixField.find('> .buttons');
      $origButtons.hide();

      // make our own container
      var $ourButtons = $('<div class="buttons pimped" />').insertAfter($origButtons),
          $ourButtonsInner = $('<div class="btngroup" />').appendTo($ourButtons);

      // loop each blockType / group pairing
      var buttonObject = buttonConfig[0]['config'];
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
            $origButtons.find('[data-type="'+type+'"]').click();
          }
        });
      });

    }

  },

  buttonConfigurator: function()
  {

    var that = this;

    // loop the fields we have on the page
    $('.pimpmymatrix-settings__list').each(function(){

      // get matrixFieldHandle and assoc stored config
      var matrixFieldHandle = $(this).data('pimpmymatrix-field-handle'),
          buttonConfig = $.grep(that.buttonConfig, function(e){ return e.fieldHandle === matrixFieldHandle; });

      // check we found a stored config
      if ( buttonConfig[0] !== undefined )
      {

        // work out the groups - couldn’t we just get these from the php?
        var configObject = buttonConfig[0].config,
            groupArray = [];

        for (var key in configObject)
        {

          if ( ! Craft.inArray(configObject[key].group, groupArray) )
          {
            // save in array for later
            groupArray.push(configObject[key].group);
          }

        }

        // loop this fields’ blockTypes
        $(this).children('li').each(function(){

          // add our group select box
          var blockTypeHandle = $(this).data('pimpmymatrix-blocktype-handle'),
              blockTypeName = $(this).data('pimpmymatrix-blocktype-name'),
              $field =$('<div class="field">'+
                '<div class="heading">'+
                  '<label for="pimpmymatrix-blocktypeselect-'+blockTypeHandle+'">'+blockTypeName+'</label>'+
                '</div>'+
                '<div class="input"><div class="select"></div></div>'+
              '</div>'),
              $select = $('<select id="pimpmymatrix-blocktypeselect-'+blockTypeHandle+'" name="pimpmymatrix-blocktypeselect-'+blockTypeHandle+'" />').appendTo($field.find('.select'));

          $('<option value="" selected></option>').appendTo($select);
          for (var key in groupArray) {
            $('<option value="'+groupArray[key]+'">'+groupArray[key]+'</option>').appendTo($select);
          }

          $(this).append($field);


          // get config from current settings by blockType.handle
          var blockTypeConfig = $.grep(configObject, function(e){ return e.blockType.handle === blockTypeHandle; });

          // check its in the settings array
          if ( blockTypeConfig[0] !== undefined )
          {
            // set value of the select
            $(this).find('select').val(blockTypeConfig[0].group);
          }

        });

      }

      // TODO:
      // watch the group table for changes and then re-populate the selects accordingly

    });


    // TODO:
    // hi-jack the save so that anything that is in our fake fields
    // gets stuck in the field before it saves
    // make sure not to add empty fake fields

  },

  /**
   * This simply returns a fieldHandle if it can get one or false if not
   */
  _getMatrixFieldName: function($matrixField)
  {
    var matrixFieldName = $matrixField.siblings('input[type="hidden"][name*="fields"]').prop('name'),
        regExp  = /fields\[([^\]]+)\]/,
        matches = regExp.exec(matrixFieldName),
        matrixFieldHandle = matches[1];

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
