const axios = require('axios');
import Toastify from 'toastify-js';

async function handleAddLike(event) {
    console.log("action :: ", event.currentTarget.dataset.action);
    const button = event.currentTarget;
    const action = button.dataset.action;
    let targetToChange = button.querySelector('.number');
    let targetToChangeIcon = button.querySelector('.icon-heart');

    $.ajax({
        type: "POST",
        url: action,
        data: "",
        success: function (data, dataType) {
            //console.log(data);
            if (data === '+1') {
                targetToChange.innerText = parseInt(targetToChange.innerText) + 1;
                $(targetToChangeIcon).removeClass("heart-dilike ");
                $(targetToChangeIcon).addClass("heart-like");
                Toastify({
                    text: "J'aime !",
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: 'left', // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    className: "success",
                    onClick: function () {
                    } // Callback after click
                }).showToast();
            } else {
                $(targetToChangeIcon).removeClass("heart-like");
                $(targetToChangeIcon).addClass("heart-dilike ");
                targetToChange.innerText = parseInt(targetToChange.innerText) - 1;
                Toastify({
                    text: "Je n'aime pas !",
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: 'left', // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    className: "error",
                    onClick: function () {
                    } // Callback after click
                }).showToast();
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {

            Toastify({
                text: "Une erreur est survenue",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        }
    });


}


async function handleAddReport(event) {
    console.log("action :: ", event.currentTarget.dataset.action);
    const button = event.currentTarget;
    const action = button.dataset.action;


    try {
        const response = await axios.post(action);
        if (response.data === "+1") {
            Toastify({
                text: "Merci de votre signalement",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        } else {
            Toastify({
                text: "Vous avez déjà signaler cela",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "error",
                onClick: function () {
                } // Callback after click
            }).showToast();
        }
    } catch (error) {
        console.error(error);
    }


}

function handleAddComment(event) {

    event.preventDefault();
    const data = $(event.target).serializeArray()[0].value;
    const action = event.target.dataset.action;
    let target = $(event.target).parent('.formComment').prev('.wrapper-comments');


    $.ajax({
        type: "POST",
        url: action,
        data: {request: data},
        success: function (data, dataType) {
            //console.log(data);
            target.append(data)
            $(function () {
                moment.locale('fr');
                $(target).find(".p-date").map((x, i) => {
                    i.innerText = moment.unix(i.dataset.createdat).local().fromNow();
                });
            });
            Toastify({
                text: "Commentaire ajouté",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {

            Toastify({
                text: "Une erreur est survenue",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        }
    });

}


function handleAddPost(event) {
    {

        event.preventDefault();
        const data = $(event.target).serializeArray()[0].value;
        const action = event.target.dataset.action;
        let target = $('.formPost.input-submit-post');


        $.ajax({
            type: "POST",
            url: action,
            data: {request: data},
            success: function (data, dataType) {
                target.after(data)
                $(function () {
                    moment.locale('fr');
                    $(target).find(".p-date").map((x, i) => {
                        i.innerText = moment.unix(i.dataset.createdat).local().fromNow();
                    });
                });
                Toastify({
                    text: "Post ajouté",
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: 'left', // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    className: "info",
                    onClick: function () {
                    } // Callback after click
                }).showToast();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {

                Toastify({
                    text: "Une erreur est survenue",
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: 'left', // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    className: "info",
                    onClick: function () {
                    } // Callback after click
                }).showToast();
            }
        });

    }

}

function hideModal(){
    $(".modal").removeClass("in");
    $(".modal-backdrop").remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
    $(".modal").hide();
}

function handleDeletePost(event) {
    {

        event.preventDefault();
        const action = event.currentTarget.dataset.action;
        let target = $(event.target).parents('.post-container');


        $.ajax({
            type: "GET",
            url: action,
            success: function (data, dataType) {

                if (data === '-1') {
                    hideModal()
                    target.remove();
                    Toastify({
                        text: "Post supprimé",
                        duration: 3000,
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: 'left', // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        className: "success",
                        onClick: function () {
                        } // Callback after click
                    }).showToast();
                } else {
                    Toastify({
                        text: "Une erreur est survenue",
                        duration: 3000,
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: 'left', // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        className: "info",
                        onClick: function () {
                        } // Callback after click
                    }).showToast();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {

                Toastify({
                    text: "Une erreur est survenue",
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: 'left', // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    className: "info",
                    onClick: function () {
                    } // Callback after click
                }).showToast();
            }
        });

    }

}

function handleDeleteComment(event) {
    {

        event.preventDefault();
        const action = event.currentTarget.dataset.action;
        let target = $(event.target).parents('.wrapper-comments');




        $.ajax({
            type: "GET",
            url: action,
            success: function (data, dataType) {
                //console.log(data);
                if (data === '-1') {
                    hideModal()
                    target.remove();
                    Toastify({
                        text: "Commentaire supprimé",
                        duration: 3000,
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: 'left', // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        className: "success",
                        onClick: function () {
                        } // Callback after click
                    }).showToast();
                } else {
                    Toastify({
                        text: "Une erreur est survenue",
                        duration: 3000,
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: 'left', // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        className: "info",
                        onClick: function () {
                        } // Callback after click
                    }).showToast();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {

                Toastify({
                    text: "Une erreur est survenue",
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: 'left', // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    className: "info",
                    onClick: function () {
                    } // Callback after click
                }).showToast();
            }
        });

    }

}


async function handleAddPostPinned(event) {
    console.log("action :: ", event.currentTarget.dataset.action);
    const button = event.currentTarget;
    const action = button.dataset.action;
    const targetToChange = button.querySelector('.pinText');
    console.log(targetToChange)

    try {
        const response = await axios.post(action);
        if (response.data === "+1") {
            targetToChange.innerText = "Post epinglé";
            Toastify({
                text: "Vous suivez ce post",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        } else {
            targetToChange.innerText = "Epingler ce post";
            Toastify({
                text: "Vous ne suivez plus ce post",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        }
    } catch (error) {
        console.error(error);
    }


}


async function handleAddUserFollow(event) {
    event.preventDefault();

    console.log("action :: ", event.currentTarget.dataset);
    const button = event.currentTarget;
    const action = button.dataset.action;
    const targetToChange = button.querySelector('.followStatus');
    console.log("targetToChange :: ", targetToChange);

    try {
        const response = await axios.post(action);
        if (response.data === "+1") {
            targetToChange.innerText = "Ne plus suivre";
            Toastify({
                text: "Abonnement ajouté",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        } else {
            targetToChange.innerText = "Suivre";
            Toastify({
                text: "Abonnement supprimer",
                duration: 3000,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function () {
                } // Callback after click
            }).showToast();
        }
    } catch (error) {
        console.error(error);
    }


}


//Comment Ajax load
function getNextComment(event) {
    event.preventDefault();

    var infiniteScroll = $('.wrapper-comments').infiniteScroll({
        // options
        path: "/comment/post/" + event.target.dataset.post + '?page={{#}}',
        append: $(event.target).closest('.content'),
        history: false,
        status: '.page-load-status',


        button: '.view-more-button',
// load pages on button click
        scrollThreshold: false,
// disable loading on scroll
    });
    infiniteScroll.on('load.infiniteScroll', function () {
        $(function () {
            moment.locale('fr');
            $(".p-date").map((x, i) => {
                i.innerText = moment.unix(i.dataset.createdat).local().fromNow();
            });
        });
    });


}


window.handleAddLike = handleAddLike;
window.handleDeletePost = handleDeletePost;
window.handleDeleteComment = handleDeleteComment;
window.handleAddReport = handleAddReport;
window.handleAddComment = handleAddComment;
window.handleAddUserFollow = handleAddUserFollow;
window.handleAddPost = handleAddPost;
window.handleAddPostPinned = handleAddPostPinned;
//window.getNextComment = getNextComment;


