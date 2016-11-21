/**
 * Exécuté au load de la page
 */
//ne pas utiliser "$(window).load(function() {" avec jQuery 3...
$(window).on('load', function() {
    manageImageLazyLoad();
    manageAjaxFormSubmit();
    manageAlertClose();
});


/**
 * Fonctions
 */
//lazy loading des images
function manageImageLazyLoad() {
    $('img.lazy').each(function(index, image){
        var $image = $(image);
        $image.attr('src', $image.data('src'));
    });
}

//permet de cacher (plutot que de supprimer) les alertes
function manageAlertClose() {
    $('.close-alert').on('click', function() {
        hideMessage();
    });
}

//montre un mesage de succes
function showSuccessMessage(msg) {
    $('.alert-success .alert-content')
        .html(msg)
        .parent()
        .removeClass('hide')
        .show()
        .addClass('animated slideInUp');
    setTimeout(function() {
        hideMessage();
    }, 5000);
}

//montre un mesage d'erreur
function showErrorMessage(msg) {
    $('.alert-danger .alert-content')
        .html(msg)
        .parent()
        .removeClass('hide')
        .show()
        .addClass('animated slideInUp');
    setTimeout(function() {
        hideMessage();
    }, 5000);
}

//cache les messages
function hideMessage() {
    $('.alert')
        .removeClass('animated slideInUp')
        .fadeOut();
}


//permet le scrollTo vers un élément 
function scrollTo(target) {
    $('html, body').animate({
        scrollTop: target.offset().top
    }, 1000);
}

//gestion du submit d'un formulaire en ajax
function manageAjaxFormSubmit() {
    $('body').on('submit', '.submit-ajax', function (e) {
        e.preventDefault();
        var $this = $(this);
        $('.btn', $this).attr('disabled', 'disabled');
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: $(this).serialize()
        })
        .done(function (data, textStatus, jqXHR) {
            if (typeof data.message !== 'undefined' && typeof data.form !== 'undefined') {
                showSuccessMessage(data.message);
            } else {
                showErrorMessage('An error occurred. Please retry.');
            }
        })
        .fail(function (data, textStatus, errorThrown) {
            if (typeof data.responseJSON !== 'undefined') {
                if (! data.responseJSON.hasOwnProperty('form')) {
                    showErrorMessage('An error occurred');
                }
                $('.submit-ajax').addClass('animated shake');
            } else {
                showErrorMessage('An error occurred : ' + errorThrown + '. Please retry.');
            }
        }).always(function(data, textStatus, errorThrown) {
            if (typeof data.form !== 'undefined') {
                $('.submit-ajax').replaceWith(data.form);
            } else if (data.responseJSON.hasOwnProperty('form')) {
                $('.submit-ajax').replaceWith(data.responseJSON.form);
            }
            $('.btn', $this).removeAttr('disabled');
            scrollTo($('.submit-ajax'));
        });
    });
}