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

  init: function()
  {
    this.$matrixContainer = $('.matrix');


    this.buttonConfig = [
      {
        "fieldHandle" : "someThingElseThatWontWork",
        "config" : [
          {
            "blockType" : "rbaOne",
            "group"     : "Text"
          }
        ]
      },
      {
        "fieldHandle" : "reallyBigArticle",
        "config" : [
          {
            "blockType" : "rbaOne",
            "group"     : "Text"
          },
          {
            "blockType" : "rbaTwo",
            "group"     : "Media"
          },
          {
            "blockType" : "rbaThree",
            "group"     : "Other"
          },
          {
            "blockType" : "rbaFour",
            "group"     : "Media"
          },
          {
            "blockType" : "rbaFive",
            "group"     : "Text"
          },
          {
            "blockType" : "rbaSix",
            "group"     : "Text"
          },
          {
            "blockType" : "rbaSeven",
            "group"     : "Other"
          },
          {
            "blockType" : "rbaEight",
            "group"     : "Text"
          },
          {
            "blockType" : "rbaNine",
            "group"     : "Media"
          },
          {
            "blockType" : "rbaTen",
            "group"     : "Other"
          },
          {
            "blockType" : "rbaEleven",
            "group"     : "Other"
          },
          {
            "blockType" : "rbaTwelve",
            "group"     : "Widget"
          }
        ]
      }
    ];


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
        if ( $ourButtons.find('.btngroup[data-pimped-group="'+buttonObject[key]['group']+'"]').length === 0 )
        {
          var $newGroup = $('<div class="btngroup hidden" data-pimped-group="'+buttonObject[key]['group']+'"></div>').appendTo($ourButtons);
          var $newGroupTrigger = $('<div class="btn menubtn">'+buttonObject[key]['group']+'</div>').appendTo($ourButtonsInner);

          // bind trigger to open group

        }

        // find sub group
        var $group = $ourButtons.find('.btngroup[data-pimped-group="'+buttonObject[key]['group']+'"]');

        // clone relavent original button to add to our new sub group
        var $newButton = $origButtons.find('[data-type="'+buttonObject[key]['blockType']+'"]').clone(true,true).appendTo($group);

      }

    }



    // set up
    // $ourButtons.find('.btngroup').addClass('hidden');
    // $ourButtons.find('.menubtn.hidden').removeClass('hidden');
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
