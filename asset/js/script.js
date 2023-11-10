//FOR SHOW NOTIF
if(document.querySelector('#msg-notif').innerHTML.length > 8) {
    document.querySelector('#msg-notif').classList.remove('hidden')
    setTimeout(() => {
        document.querySelector('#msg-notif').classList.add('hidden')
      }, "2000");
}

//FOR SHOW MORE OPTIONS ON ADD TASK
document.querySelector('#btn-more-options').addEventListener('click', function (event)  {
    document.querySelector('#more-option').classList.toggle('hidden')
    if (event.target.innerHTML === 'More options') {
        document.querySelector('#btn-more-options').innerHTML = 'Less options'
    }
    else {
        event.target.innerHTML = 'More oprtions'
    }
})