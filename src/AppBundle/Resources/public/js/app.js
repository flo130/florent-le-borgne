//ne pas utiliser "$(window).load(function() {" avec jQuery 3...
$(window).on('load', function() {
    //lazy loading des images
    $('img.lazy').each(function(index, image){
        var $image = $(image);
        $image.attr('src', $image.data('src'));
    });
});