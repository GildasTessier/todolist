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

// FOR ADD ICON WHEN CALL BACK DATE IS TODAY
console.log(document.querySelectorAll('.task'));
let date = new Date();
let dateNow = date.toISOString().split('T')[0]

document.querySelectorAll('.task').forEach((task) => {
    if(task.querySelector('.span-date-alert') == null) return;
    let dateAlerte = task.querySelector('.span-date-alert').innerHTML
    if (dateAlerte != dateNow) return;
    let text = '⏰ ' + task.querySelector('.title-task').getAttribute('value') + ' ⏰'
    task.querySelector('.title-task').setAttribute('value', text);
});