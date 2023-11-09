//FOR SHOW NOTIF
if(document.querySelector('#msg-notif').innerHTML.length > 8) {
    document.querySelector('#msg-notif').classList.remove('hidden')
    setTimeout(() => {
        document.querySelector('#msg-notif').classList.add('hidden')
      }, "2000");
}