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

    this.addListener(this.$matrixContainer, 'resize', 'addBlockHeadings');

    this.addListener(Garnish.$win, 'load', 'addBlockHeadings');
  },

  addBlockHeadings: function()
  {

    this.$matrixContainer.find('.matrixblock').each(function()
    {

      var $elem = $(this);

      if ( !$elem.data('pimped') )
      {
        $elem.data('pimped', true);
        $elem.prepend('<div class="pimpmymatrix-heading">WHO ARE YOU</div>')
      }

    });
  }

});

})(jQuery);
