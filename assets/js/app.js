/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
const axios = require('axios').default;
const emailRegex = /^[a-zA-Z0-9_.+-]+@(?:(?:[a-zA-Z0-9-]+\.)?[a-zA-Z]+\.)?(bio-rad)\.com$/
const passwordRegex = /(?=^.{6,20}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[Aa-zZ])(?=.*[0-9])(?=.*[@#$%]).*$/
const ratingRegex = /(?=^.$)[0-5]+/

let formMain = $('.formMain');
let inputEmail = $('#inputEmail');
let inputPassword = $('#inputPassword');
let textarea = $('textarea');
let rating = $('#comment_rating');
let subRating = $('.subRating');
let comment = $('#comment_content').val();

$('.login').click(function (e) {
    if (inputEmail.val() === "") {
        e.preventDefault()
        $('.error_message_mail').fadeIn(1000).text("Veuillez entrer un mail").css({
            borderColor : 'red', color : 'red'
        });
    } else {
        $('.error_message_mail').fadeOut(200).text('').css({
            borderColor : 'green', color : 'green'
        });
    }

    if (inputPassword.val() === "") {
        e.preventDefault()
        $('.error_message_password').fadeIn(1000).text("Veuillez entrer un mot de passe").css({
            borderColor : 'red',color : 'red'
        });
    } else {
        $('.error_message_password').fadeOut(200).text('').css({
            borderColor : 'green', color : 'green'
        });
    }

    if(!inputPassword.val().match(passwordRegex)){
        $('.error_message_password_constraint').html('Le mot de passe doit comporter au moins 6 caractères , une majuscule ,un caractère numérique et un caractère  spécial.').css({border:'red', color:'red'});
        e.preventDefault();
    }
});

$('.btn-block').click(function (event) {
    $(formMain).each(function () {
        for (let i = 0; i < formMain.length; i++) {
            if ($(this).val() === "") {
                $('.error_message').show(200).text("** Tous les champs doivent être rempli **");
                $(this).css({borderColor : 'red', color : 'red'});
                event.preventDefault();
            }
            formMain.keyup(function(){
                $(this).css({borderColor : 'green', color : 'green'});
                $('.error_message').hide();
            });
        }
    })
});



/***************************************** check login *************************************/



/********************************* reset password ******************************************/
$('#resetMdp').submit(function (event) {
   if ($('#confirmNewPassword').val() !== $('#newPassword').val()) {
        $('#confirmPasswordCheck').html('** Les mots de passe ne correspondent pas **').css('color','red');
        event.preventDefault();
    }
});


$('#valider').click(function (e) {
    if (!$('#newPassword').val().match(passwordRegex)) {
        $('#newPasswordCheck').html('Le mot de passe doit comporter au moins 6 caractères , une majuscule ,un caractère numérique et un caractère  spécial.').css('color','red');
        e.preventDefault();
    }
});

/********************************** enregistrement *******************************************/

/*$('#registration_form_email').keyup(function (e) {
    if(!$(this).val().match(emailRegex)) {
        $('.error_message_match').show(200).text("Veuillez entrer un email de type prénom_nom@bio-rad.com");
        $(this).css({borderColor : 'red', color : 'red'});
        e.preventDefault()
    }
})*/

$('.subscriber').submit(function (e) {
    if ($('#registration_form_plainPassword_second').val() !== $('#registration_form_plainPassword_first').val()) {
        console.log('match')
        $('.passwordCheck').html('** Les mots de passe ne correspondent pas **').css({border:'red', color:'red'});
        e.preventDefault();
    }
});

$('#enregistrer').click(function (e) {
    if (!$('#registration_form_plainPassword_first').val().match(passwordRegex)) {
        console.log('regex')
        $('.error_message_password_constraint').html('Le mot de passe doit comporter au moins 6 caractères , une majuscule ,un caractère numérique et un caractère  spécial.').css({border:'red', color:'red'});
        e.preventDefault();
    }
});

/******************************************  rating   *********************************************/


subRating.click(function (e) {
    if((!rating.val().match(ratingRegex)) || rating.val() === "") {
        $('.error-message-rating').show().text('La valeur de la note ne peut pas être nul');
        e.preventDefault();
    } else {
        $('.error-message-rating').hide();
    }
    if (comment === "") {
        e.preventDefault();
    }
});

rating.keyup(function(){
    if(!rating.val().match(ratingRegex)  ) {
        $('.error-message-rating').show().text('La note doit être une valeur numérique et doit etre comprise entre 1 et 5');
        rating.css("borderColor", "red");
    } else {
        rating.css("borderColor", "green");
        $('.error-message-rating').hide();
    }
});






/*********************************************** like ***********************************************/

function onClickBtnLike(event){
    event.preventDefault();
    const url = this.href;
    const spanCount = this.querySelector('span.js-likes');
    const icone = this.querySelector('i');

    axios.get(url).then(function(response) {
        spanCount.textContent=response.data.postLikes;
        if(icone.classList.contains('fas')) {
            icone.classList.replace('fas','far');
        } else {
            icone.classList.replace('far','fas')
        }
    }) .catch(function(error) {
        if(error.status === 403 ) {
            window.alert("Une erreur s'est produite, réessayer plus tard");
        } else {
            window.alert("Vous ne pouvez liker une vidéo si vous n'êtes pas connecté!");
        }

    })
}

document.querySelectorAll('a.js-like').forEach(function(link) {
    link.addEventListener('click',onClickBtnLike);
});

/********************************* profile ********************************************/
/*let menu = $("div.user-menu>div.user-menu-content");

$(document).ready(function() {
    const $btnSets = $('#responsive'),
        $btnLinks = $btnSets.find('a');

    $btnLinks.click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        const index = $(this).index();
        menu.removeClass("active");
        menu.eq(index).addClass("active");
    });
});

$(document).ready(function() {
    $("[rel='tooltip']").tooltip();

    $('.view').hover(
        function(){
            $(this).find('.caption').slideDown(550); //.fadeIn(250)
        },
        function(){
            $(this).find('.caption').slideUp(550); //.fadeOut(205)
        }
    );
});*/

/******************* up load file *************************/

