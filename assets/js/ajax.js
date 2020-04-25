const axios = require('axios');

async function handleAddLike(event) {
    console.log("action :: ", event.currentTarget.dataset.action)
    const button =  event.currentTarget
    const action = button.dataset.action
    const targetToChange = button.querySelector('.number')

    try {
        const response = await axios.post(action);
        if(response.data === "+1"){
            targetToChange.innerText =  parseInt(targetToChange.innerText) + 1 ;
        }else{
            targetToChange.innerText =  parseInt(targetToChange.innerText) - 1 ;
        }
    } catch (error) {
        console.error(error);
    }

}


async function handleAddReport(event) {
    console.log("action :: ", event.currentTarget.dataset.action)
    const button =  event.currentTarget
    const action = button.dataset.action


    try {
        const response = await axios.post(action);
        if(response.data === "+1"){
            Toastify({
                text: "+1",
                duration: 3000,
                destination: "https://github.com/apvarun/toastify-js",
                newWindow: true,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function(){} // Callback after click
            }).showToast();
        }else{
            Toastify({
                text: "+0",
                duration: 3000,
                destination: "https://github.com/apvarun/toastify-js",
                newWindow: true,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: 'left', // `left`, `center` or `right`
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                stopOnFocus: true, // Prevents dismissing of toast on hover
                className: "info",
                onClick: function(){} // Callback after click
            }).showToast();
        }
    } catch (error) {
        console.error(error);
    }


}


function handleAddPostReport(event) {
    console.log('ok')
}


function handleAddComment(event) {
    console.log('ok')

}


function handleAddPost(event) {
    console.log('ok')

}


async function handleAddPostPinned(event) {
    console.log("action :: ", event.currentTarget.dataset.action)
    const button =  event.currentTarget
    const action = button.dataset.action
    const targetToChange = button.querySelector('.number')

    try {
        const response = await axios.post(action);
        if(response.data === "+1"){
            targetToChange.innerText =  parseInt(targetToChange.innerText) + 1 ;
        }else{
            targetToChange.innerText =  parseInt(targetToChange.innerText) - 1 ;
        }
    } catch (error) {
        console.error(error);
    }


}


async function handleGetComment(event) {
    try {
        const response = await axios.get('/user?ID=12345');
        console.log(response);
    } catch (error) {
        console.error(error);
    }
}


//Comment Ajax load
function getNextComment(event) {
    var compteur = 0;
    event.preventDefault()
    console.log(parseInt(event.target.dataset.page) + 1)
    var url = $(event.target).attr('href');
    var newUrl = "/comment/post/" + event.target.dataset.post + "?page=" + (parseInt(event.target.dataset.page) + 1);
    const containerComment = $(event.target).next('.nextComment')[0];

    $.get(url, function (data, response) {
        if (response == "success") {

            $(data).prependTo(containerComment).fadeIn("slow");
            $(event.target).attr('href', newUrl)
            $(event.target).attr('data-page', (parseInt(event.target.dataset.page) + 1))
        }
        if (response == "error") {
            console.log("Error: " + xhr.status + ": " + xhr.statusText);
        }
    });
}


window.handleAddLike = handleAddLike;
window.handleAddReport = handleAddReport;
window.handleAddComment = handleAddComment;
window.handleAddPost = handleAddPost;
window.handleAddPostPinned = handleAddPostPinned;
window.handleGetComment = handleGetComment;
window.getNextComment = getNextComment;
