///////////////////////
// Variables globles //
///////////////////////
var TIMEOUT_TO_CLOSE_MSG = 5000;
var TIMEOUT_TO_SCROLL_TO = 1000;


////////////////////////////////
// Exécuté au load de la page //
////////////////////////////////
$(window).on('load', function() {
    //ne pas utiliser "$(window).load(function() {" avec jQuery 3...
    manageImageLazyLoad();
    manageAjaxFormSubmit();
    manageAlertClose();
    manageNavTab();
});


//////////////////////
// Fonctions utiles //
//////////////////////
/**
 * Permet d'arriver directement sur un onglet bootstrap (ayant la class "nav-hashtag") 
 * en mettant comme hash dans l'URL, son id
 * 
 * @return void
 */
function manageNavTab()
{
	if (document.location.toString().match('#')) {
		$('.nav-hashtag a[href="#' + document.location.toString().split('#')[1] + '"]').tab('show') ;
	} 
	$('.nav-hashtag a').on('shown.bs.tab', function (e) {
		if(history.pushState) {
			history.pushState(null, null, e.target.hash); 
		} else {
			window.location.hash = e.target.hash; 
		}
	});
}

/**
 * Gère le lazy loading des images.
 * Remplace créé un attribut "src" avec les données de l'attibut "data-src" au load 
 * de la page
 * 
 * @return void
 */
function manageImageLazyLoad()
{
    $('img.lazy').each(function(index, image){
        var $image = $(image);
        $image.attr('src', $image.data('src'));
    });
}

/**
 * Permet de cacher (plutot que de supprimer) une alerte ouverte lors du clique sur 
 * le bouton close
 * 
 * @return void
 */
function manageAlertClose()
{
    $('.close-alert').on('click', function() {
        hideMessage();
    });
}

/**
 * Affiche un message de succes (qui se fermera automatiquement)
 * 
 * @param string msg
 * 
 * @return void
 */
function showSuccessMessage(msg)
{
    $('.alert-success .alert-content')
        .html(msg)
        .parent()
        .removeClass('hide')
        .show()
        .addClass('animated slideInUp');
    setTimeout(function() {
        hideMessage();
    }, TIMEOUT_TO_CLOSE_MSG);
}

/**
 * Affiche un message d'erreur (qui se fermera automatiquement)
 * 
 * @param string msg
 * 
 * @return void
 */
function showErrorMessage(msg)
{
    $('.alert-danger .alert-content')
        .html(msg)
        .parent()
        .removeClass('hide')
        .show()
        .addClass('animated slideInUp');
    setTimeout(function() {
        hideMessage();
    }, TIMEOUT_TO_CLOSE_MSG);
}

/**
 * cache tous les messages ouverts
 * 
 * @return void
 */
function hideMessage()
{
    $('.alert')
        .removeClass('animated slideInUp')
        .fadeOut();
}

/**
 * Gère un "scrollTo" vers un élément de la page
 * 
 * @param jQuery target
 * 
 * @return void
 */ 
function scrollTo(target)
{
    $('html, body').animate({
        scrollTop: target.offset().top
    }, TIMEOUT_TO_SCROLL_TO);
}

/**
 * Gère le submit d'un formulaire en ajax (pour soumettre en ajax, il faut 
 * que le form est la class css "submit-ajax") : 
 * - Si tout est ok : 
 *     - soit on affiche un message et on reste sur le form
 *     - soit, on redirige vers une autre page
 * - S'il y a une erreur : 
 *     - soit on réaffiche le form (s'il est là)
 *     - soit on affiche un message
 * 
 * @return void
 */
function manageAjaxFormSubmit()
{
    $('body').on('submit', '.submit-ajax', function (e) {
        e.preventDefault();
        var $this = $(this);
        //désactive le bouton submit
        $('.btn', $this).attr('disabled', 'disabled');
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            //$(this).serialize() ne gère pas les fileUpload, il faut utiliser l'objet FormData
            //data: $(this).serialize()
            data: new FormData(this),
            processData: false,
            contentType: false
        })
        //requete ajax a réussie, on affiche le message
        .done(function (data, textStatus, jqXHR) {
            //s'il y a une partie "redirect", on redirige vers la page. Sinon on affiche le message
            if (typeof data.redirect !== 'undefined') {
                document.location.href = data.redirect;
            } else {
                if (typeof data.message !== 'undefined' && typeof data.form !== 'undefined') {
                    showSuccessMessage(data.message);
                } else {
                    showErrorMessage('An error occurred. Please retry.');
                }
            }
        })
        //la requete ajax a échouée, on affiche le message
        .fail(function (data, textStatus, errorThrown) {
            if (typeof data.responseJSON !== 'undefined') {
                if (! data.responseJSON.hasOwnProperty('form')) {
                    showErrorMessage('An error occurred');
                }
                $('.submit-ajax').addClass('animated shake');
            } else {
                showErrorMessage('An error occurred : ' + errorThrown + '.');
            }
        })
        //dans tous les cas (requete réussie ou échouée), on remplace le form actuel par celui retourné par le serveur
        .always(function(data, textStatus, errorThrown) {
            if (typeof data.form !== 'undefined') {
                $('.submit-ajax').replaceWith(data.form);
            } else if (typeof data.responseJSON !== 'undefined' && data.responseJSON.hasOwnProperty('form')) {
                $('.submit-ajax').replaceWith(data.responseJSON.form);
            }
            $('.btn', $this).removeAttr('disabled');
            scrollTo($('.submit-ajax'));
        });
    });
}