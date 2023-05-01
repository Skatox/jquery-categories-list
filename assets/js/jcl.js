function jquery_categories_list_animate(clickedObj, options) 
{
    var changeSymbol = function (){
        jQuery(clickedObj).children('.jcl_symbol').html(options['ex_sym'])
    }

    if (jQuery(clickedObj).siblings('ul').children('li').is(':hidden')) {
        jQuery(clickedObj).children('.jcl_symbol').html(options['con_sym'])

        if(options['fx_in'] === 'fadeIn')
            jQuery(clickedObj).siblings('ul').children('li').fadeIn()
        else if(options['fx_in'] === 'slideDown')
            jQuery(clickedObj).siblings('ul').children('li').slideDown()
        else
            jQuery(clickedObj).siblings('ul').children('li').show()
    } else {
        if(options['fx_in'] === 'fadeIn')
            jQuery(clickedObj).siblings('ul').children('li').fadeOut('', changeSymbol)
        else if(options['fx_in'] === 'slideDown')
            jQuery(clickedObj).siblings('ul').children('li').slideUp('', changeSymbol)
        else
            jQuery(clickedObj).siblings('ul').children('li').hide(0, changeSymbol)
    }
    jQuery(clickedObj).parent().toggleClass('expanded')
}

jQuery(function() 
{
    jQuery('.jcl_widget').each(function(index){
        var elementsClicked = 'a',
            options = {
                fx_in: jQuery(this).siblings('.fx_in').val(),
                ex_sym: jQuery(this).siblings('.ex_sym').val(),
                con_sym: jQuery(this).siblings('.con_sym').val()
            }

        if(!jQuery(this).siblings('.parent_expand').length)
            elementsClicked = 'a.jcl_link';
        
        jQuery(this).on('click', elementsClicked, function(e){
             if (jQuery(this).siblings('ul').children('li').length) e.preventDefault()
             jquery_categories_list_animate(this, options)
        })
    })
});