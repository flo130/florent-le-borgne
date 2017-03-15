///////////////////////
// Variables globles //
///////////////////////
var TIMEOUT_TO_CLOSE_MSG = 5000;
var TIMEOUT_TO_SCROLL_TO = 1000;
var URL_CATEGORY_RENAME = '/' + findLanguage() + '/admin/category/rename';
var URL_CATEGORY_CREATE = '/' + findLanguage() + '/admin/category/create';
var URL_CATEGORY_DELETE = '/' + findLanguage() + '/admin/category/delete';
var URL_CATEGORY_MOVE = '/' + findLanguage() + '/admin/category/move';


////////////////////////////////////////////////////
// Ecouteurs/actions exécutées au load de la page //
////////////////////////////////////////////////////
$(window).on('load', function() {
    //ne pas utiliser "$(window).load(function() {" avec jQuery 3...
    manageImageLazyLoad();
    manageAlertClose();
    manageNavTab();
    manageTooltip();
    manageConfirmModals();
    manageAjaxFormSubmit();
    manageArticleCommentAjaxFormSubmit();
    manageAjaxPagination();
    manageTableSearch();
    manageHighlightBlock();
    manageMenuTree();
    manageMenuTreeJs();
});


//////////////////////
// Fonctions utiles //
//////////////////////
/**
 * Permet la gestion des fleches d'un menu type "arbre" utilisant les icones Bootstrap
 * (sur le front)
 * 
 * @return void
 */
function manageMenuTree() {
    $('.list-group-tree .list-group-item').on('click', function() {
        $('.glyphicon', this)
            .toggleClass('glyphicon-chevron-right')
            .toggleClass('glyphicon-chevron-down');
    });
}

/**
 * Permet la gestion d'un menu type "arbre" (ajout / suppression / modification / présentation)
 * En utilisant la lib jsTree
 * (sur le back)
 * 
 * @return void
 */
function manageMenuTreeJs() {
    $('.jstree').jstree({
        "core" : {
            //durée de l'annimation lorsqu'on déplis une catégorie
            "animation" : 200,
            //est-ce qu'on autorise les callbacks sur les actions utilisateur
            "check_callback" : true,
            "themes" : {
                "icons" : false,
                "reponsive" : true
            }
        },
        //ici pour voir la liste des plugin : https://www.jstree.com/plugins/
        "plugins" : [
            //plugin pour afficher les option (create/delete/rename) sur un click droit
            "contextmenu", 
            //plugin drag and drop pour glisser déposer les noeuds
            "dnd",
            //plugin qui va sauvegarder l'état de l'arbre dans le navigateur pour le réafficher tel quel lorsqu'on reviendra sur la page
            "state"
        ]
    })
    .on('rename_node.jstree', function (e, data) {
        $.post(URL_CATEGORY_RENAME, { 
            'id' : data.node.id, 
            'title' : data.text
        }).done(function(response) {
            if (response.status == true) {
                showSuccessMessage(response.message);
            } else {
                showErrorMessage(response.message);
            }
        });
    })
    .on('delete_node.jstree', function (e, data) {
        $.post(URL_CATEGORY_DELETE, { 
            'id' : data.node.id
        }).done(function(response) {
            if (response.status == true) {
                showSuccessMessage(response.message);
                location.reload(true);
            } else {
                showErrorMessage(response.message);
            }
        });
    })
    .on('move_node.jstree', function (e, data) {
        $.post(URL_CATEGORY_MOVE, { 
            'currentId' : data.node.id,
            'parentId' : data.node.parent
        }).done(function(response) {
            if (response.status == true) {
                showSuccessMessage(response.message);
            } else {
                showErrorMessage(response.message);
            }
        });
    })
    .on('create_node.jstree', function (e, data) {
        $.post(URL_CATEGORY_CREATE, {
            'category_form[parent]' : data.parent,
            'category_form[title]' : 'title'
        }).done(function(response) {
            //modifie l'id du nouveau noeud par l'id créé en base
            data.instance.set_id(data.node, response.id);
        });
    });
}

/**
 * Permet la gestion de la coloration syntaxique des blocs de code
 * (utilise la lib highlightBlock)
 * 
 * @return void
 */
function manageHighlightBlock() {
    $('pre').each(function(i, block) {
        hljs.highlightBlock(block);
    });
}

/**
 * Permet la gestion du comportement des modals de type "confirm" (modal Bootstrap et non popup navigateur)
 * 
 * @return void
 */
function manageConfirmModals() {
    $('.print-confirm').on('click', function(e) {
        //supprime le comportement du lien par défaut
        e.preventDefault();
        e.stopPropagation();
        //on remplis le href du bouton ok de la modal par l'URL de l'action à effectuée suite à la confirmation
        $('#modal-confirm-btn-ok').attr('href', $(this).attr('href'));
        $('#modal-confirm').modal('show');
    });
}

/**
 * Gère le comportement du Tooltip Bootstrap
 * 
 * @return void
 */
function manageTooltip() {
    $('[data-toggle="tooltip"]').tooltip();
}

/**
 * Gère la recherche coté client via la librairie DataTable
 * 
 * @return void
 */
function manageTableSearch() {
    //construit le paramétrage du DataTable
    var params = {
        //affiche les bouton "dernier", "suivants", "premier", "précedent" (autrement seul "suivant" et "précédent" sont affichés)
        'pagingType': 'full_numbers',
        //garde l'état du tableau entre le rechargement de deux pages
        stateSave: true
    };
    //passe la langue en français si besoin (sinon ça sera l'anglais) en ajoutant l'option dans le paramétrage
    if (findLanguage() == 'fr') {
        params['language'] = {
            'url': 'http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json'
        };
    }
    //initialise le DataTable sur les éléments ayant la class 'table-search'
    $('.table-search').DataTable(params);
}

/**
 * Retourne la langue utilisée (on regarde le paramètre passé dans l'URL courante)
 * 
 * @return string
 */
function findLanguage() {
    var link = document.createElement("a");
    link.href = window.location.href;
    var pieces = link.pathname.split("/");
    return pieces[1];
}

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
    $('.close-alert').on('click', function(e) {
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
 * que le form ait la class css "submit-ajax") : 
 * - si tout est ok : 
 *     - soit on affiche un message et on reste sur le form
 *     - soit, on redirige vers une autre page
 * - s'il y a une erreur : 
 *     - soit on réaffiche le form (s'il est là)
 *     - soit on affiche un message
 * 
 * @return void
 */
function manageAjaxFormSubmit()
{
    $('body').on('submit', '.submit-ajax', function (e) {
        var $this = $(this);
        //desactive le submit par défaut
        e.preventDefault();
        //désactive le bouton submit
        $('.btn', $this).attr('disabled', 'disabled');
        //envoie les données au serveur
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (data, textStatus, jqXHR) {
            //s'il y a une partie "redirect", on redirige vers la page. 
            //sinon on affiche un message
            if (typeof data.redirect !== 'undefined') {
                document.location.href = data.redirect;
            } else {
                if (typeof data.message !== 'undefined' && typeof data.form !== 'undefined') {
                    showSuccessMessage(data.message);
                } else {
                    showErrorMessage('An error occurred. Please retry.');
                }
            }
        }).fail(function (data, textStatus, errorThrown) {
            if (typeof data.responseJSON !== 'undefined') {
                if (! data.responseJSON.hasOwnProperty('form')) {
                    //le serveur n'a pas retourné de formulaire, on affiche donc une erreur générale
                    showErrorMessage('An error occurred');
                } else {
                    //la requete est en erreur mais on a quand même récupéré un formulaire depuis le serveur (erreur de saisie surement, controle de champs).
                    //du coup on lui applique une petite annimation pour montrer l'erreur à l'utilisateur...
                    $('.submit-ajax').parent().addClass('animated shake');
                    //une fois l'animation terminée, on retire les classes css qui ont servie
                    setTimeout(function() {
                        $('.submit-ajax').parent().removeClass('animated shake');
                    }, 500);
                }
            } else {
                //erreur générale
                showErrorMessage('An error occurred : ' + errorThrown + '.');
            }
        }).always(function(data, textStatus, errorThrown) {
            //remplace le form actuel par celui retourné par le serveur
            if (typeof data.form !== 'undefined') {
                $('.submit-ajax').replaceWith(data.form);
            } else if (typeof data.responseJSON !== 'undefined' && data.responseJSON.hasOwnProperty('form')) {
                $('.submit-ajax').replaceWith(data.responseJSON.form);
            }
            //réactive le bouton submit
            $('.btn', $this).removeAttr('disabled');
            //s'il y a des images, il faut relancer le lazy load
            manageImageLazyLoad();
        });
    });
}

/**
 * Permet la récupération des éléments d'une liste paginée en ajax (il faut que les 
 * éléments de pagination aient la class css "pagination-ajax")
 * 
 * @return void
 */
function manageAjaxPagination() 
{
    $('body').on('click', '.pagination-ajax', function (e) {
        var $this = $(this);
        //désactive le lien par défaut
        e.preventDefault();
        //envoie les données au serveur
        $.get($this.attr('href'), function(data) {
            //remplace le contenu et la pagination existante avec les données reçues
            $('#content').replaceWith(data.content);
            $('#pagination').replaceWith(data.pagination);
            //s'il y a des images dans le nouveau contenu, il faut relancer le lazy load
            manageImageLazyLoad();
            //scroll vers le haut pour voir les nouvelles données
            scrollTo($('#content'));
        }).fail(function () {
            showErrorMessage('An error occurred');
        });
    });
}

/**
 * Gère le submit du formulaire d'ajout de commentaires
 * cf. manageAjaxFormSubmit() => un peu pareil
 * 
 * @return void
 */
function manageArticleCommentAjaxFormSubmit()
{
    $('body').on('submit', '#article_comment_form', function (e) {
        var $this = $(this);
        e.preventDefault();
        $('.btn', $this).attr('disabled', 'disabled');
        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (data, textStatus, jqXHR) {
            if (typeof data.comment !== 'undefined') {
                $("#comments").append(data.comment).show(200);
            } else {
                showErrorMessage('An error occurred.');
            }
        }).fail(function (data, textStatus, errorThrown) {
            if (typeof data.responseJSON !== 'undefined') {
                if (! data.responseJSON.hasOwnProperty('form')) {
                    showErrorMessage('An error occurred');
                } else {
                    $('#article_comment_form').replaceWith(data.form);
                    $('#article_comment_form').addClass('animated shake');
                }
            } else {
                showErrorMessage('An error occurred : ' + errorThrown + '.');
            }
        }).always(function(data, textStatus, errorThrown) {
            $('.btn', $this).removeAttr('disabled');
            manageImageLazyLoad();
        });
    });
}