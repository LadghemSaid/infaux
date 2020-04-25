import Toastify from 'toastify-js';


//const _receiver = document.getElementById('mercure-content-receiver');

function startServiceWorker(url) {
    const numberNotifs = $('.number_notif');

    navigator.serviceWorker.register('/service-worker.js');
    Notification.requestPermission(function (result) {
        if (result === 'granted') {
            navigator.serviceWorker.ready.then(function (registration) {
                const eventSource = new EventSourcePolyfill(url, {
                    headers: {
                        'Authorization': 'Bearer {{ bearerToken }}'
                    }
                });

                eventSource.onmessage = (evt) => {
                    const data = JSON.parse(evt.data);
                    if (!data.message) {
                        return;
                    }
                    numberNotifs[0].innerText++;
                    //  notifyMe(data.message);
                    Toastify({
                        text: "Tu as reçu une notifications !",
                        duration: 3000,
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: 'left', // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        className: "success",
                        onClick: function () {
                        } // Callback after click
                    }).showToast();
                    console.log(data);
                    registration.showNotification('Tu as reçu une notifications !', {
                        body: data.message,
                        icon: '/img/logo_infaux.png',
                    });

                };
            })

        } else {
            console.log("Notification refused")
        }
    })
}

window.startServiceWorker = startServiceWorker;




