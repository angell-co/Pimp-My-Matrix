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
 *
 */
PimpMyMatrix.BlockAdjuster = Garnish.Base.extend(
{

  init: function(settings)
  {

    // Set up
    // this.setSettings(settings, PimpMyMatrix.BlockAdjuster.defaults);


    // this.addListener($matrixField, 'resize', 'addBlockHeadings');
  },

  // addBlockHeadings: function()
  // {
  //
  //   // loop available matrix fields
  //   this.$matrixContainer.each($.proxy(function()
  //   {
  //
  //     // get field and blockTypes
  //     var $matrixField = $(this),
  //         blockTypes = $matrixField.data('blockTypes');
  //
  //     // loop blocks
  //     $matrixField.find('.matrixblock').each($.proxy(function()
  //     {
  //
  //       // cache block
  //       var $block = $(this);
  //
  //       // final check that we haven't already added one in case something has gone mental
  //       if ( ! $block.hasClass('pimped') )
  //       {
  //
  //         // get the block type handle from DOM
  //         var blockTypeHandle = $block.find('input[type="hidden"][name*="[type]"]').val();
  //
  //         // using the blockTypes, match the handle to the blockType object
  //         var result = $.grep(blockTypes, function(e){ return e.handle === blockTypeHandle; });
  //
  //         if (result.length > 0)
  //         {
  //
  //           // add the block name
  //           $block.addClass('pimped');
  //           $block.prepend('<div class="pimpmymatrix-heading">'+result[0].name+'</div>')
  //
  //         }
  //
  //       }
  //
  //     }), this);
  //
  //
  //   }), this);
  //
  // },
  //
  // initBlockHeadings: function()
  // {
  //
  //   // loop available matrix fields
  //   this.$matrixContainer.each($.proxy(function()
  //   {
  //
  //     // get field and blockTypes
  //     var $matrixField = $(this),
  //         blockTypes = $matrixField.data('blockTypes');
  //
  //     // if we have blockTypes
  //     if (typeof blockTypes !== "undefined") {
  //
  //       // get elementIds of the blocks and return array of blockElementIds paired with blockTypeIds
  //       var elementIds = $matrixField.find('.matrixblock').map(function() {
  //         return $(this).data('id');
  //       }).get();
  //
  //       Craft.postActionRequest('pimpMyMatrix/getBlocks', { elementIds : elementIds }, $.proxy(function(response, textStatus)
  //       {
  //         if (textStatus === 'success')
  //         {
  //           if (response.success)
  //           {
  //
  //             // now we have an array of block models, store them
  //             $matrixField.data('blocks', response.blocks);
  //
  //             // loop blocks
  //             $matrixField.find('.matrixblock').each($.proxy(function()
  //             {
  //
  //               // cache block
  //               var $block = $(this);
  //
  //               // final check that we haven't already added one in case something has gone mental
  //               if ( ! $block.hasClass('pimped') )
  //               {
  //
  //                 // get the block element id
  //                 var blockElementId = $block.data('id');
  //
  //                 // using the elementId, match up the block model
  //                 var block = $.grep($matrixField.data('blocks'), function(e){ return e.id == blockElementId; });
  //
  //                 if (block.length > 0)
  //                 {
  //
  //                   block = block[0];
  //
  //                   var blockType = $.grep(blockTypes, function(e){ return e.id == block.typeId; });
  //
  //                   if (blockType.length > 0)
  //                   {
  //
  //                     // add the block name
  //                     $block.addClass('pimped');
  //                     $block.prepend('<div class="pimpmymatrix-heading">'+blockType[0].name+'</div>')
  //
  //                   }
  //
  //                 }
  //
  //               }
  //
  //             }), this);
  //
  //           }
  //         }
  //       }), this);
  //
  //     }
  //
  //   }), this);
  //
  // },
  //
},
{
  defaults: {

  }
});


})(jQuery);
