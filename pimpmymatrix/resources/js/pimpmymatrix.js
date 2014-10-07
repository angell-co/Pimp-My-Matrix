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

  $matrixContainer: null,

  init: function()
  {
    this.$matrixContainer = $('.matrix');

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
    var that = this;

    // get matrix field handle out of DOM
    var matrixFieldName = $matrixField.siblings('input[type="hidden"][name*="fields"]').prop('name'),
        regExp  = /fields\[([^\]]+)\]/,
        matches = regExp.exec(matrixFieldName),
        matrixFieldHandle = matches[1];

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
    $matrixField.find('> .buttons').clone(true, true).appendTo($matrixField);
  }

});

})(jQuery);
