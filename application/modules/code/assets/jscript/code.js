$(document).ready(function() {
    $('.code_block').each(function(index, item) {
        //---read config from item
        config = {
            readOnly:true,
            theme: $(item).attr('theme'),
            lang: $(item).attr('lang')
        };
        $(item).ace(config);
    })

});