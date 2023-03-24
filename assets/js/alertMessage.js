var alertPlaceholder = document.getElementById('liveAlertPlaceholder')
var alertExist = document.getElementsByClassName('liveAlertBtn')

function alert(message, type) {
    var wrapper = document.createElement('div')
    wrapper.innerHTML = '<div class="close-element ' + type + ' alert-dismissible" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
    alertPlaceholder.append(wrapper)
}

if (alertExist) {
    Array.from(alertExist).forEach((oneAlertExist)=>{
        alert(oneAlertExist.innerHTML, oneAlertExist.className)
    })

    setTimeout(function () {
        const elements = document.querySelectorAll(".close-element");
        fadeIn(elements)
    }, 5000);
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function fadeIn(elements)
{
    for (let nbr = 0 ; nbr < elements.length ; nbr++){
        for (let opacity = 1; opacity > 0; opacity= opacity-0.1){
            await pause(100);
            elements[nbr].style.opacity = opacity;
        }
        elements[nbr].parentNode.removeChild(elements[nbr]);
        await pause(1000)
    }
}
