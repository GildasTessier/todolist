//FOR SHOW NOTIF
if (document.querySelector('#msg-notif').innerHTML.length > 8) {
    document.querySelector('#msg-notif').classList.remove('hidden')
    setTimeout(() => {
        document.querySelector('#msg-notif').classList.add('hidden')
    }, "2000");
}

//FOR SHOW MORE OPTIONS ON ADD TASK
document.querySelector('#btn-more-options').addEventListener('click', function (event) {
    document.querySelectorAll('.js-more-option').forEach(node => {
        node.classList.toggle('hidden')
    });
    event.target.innerHTML = (event.target.innerHTML === 'More options') ?  'Less options' : 'More options';
})

// FOR ADD ICON WHEN CALL BACK DATE IS TODAY
let date = new Date();
let dateNow = date.toISOString().split('T')[0]

document.querySelectorAll('.task').forEach((task) => {
    if (task.querySelector('.span-date-alert') == null) return;
    let dateAlerte = task.querySelector('.span-date-alert').innerHTML
    if (dateAlerte != dateNow) return;
    let text = '⏰ ' + task.querySelector('.title-task').getAttribute('value') + ' ⏰'
    task.querySelector('.title-task').setAttribute('value', text);
});


// FOR DISPLAY MENU BURGER 
document.querySelector('#icon-menu-burger').addEventListener('click', function () {
    document.querySelector('#burger-menu').classList.toggle('hidden')
})

// FOR DISPLAY FORM CREATE ACCOUNT
document.querySelectorAll('.js-change-form').forEach((btn) => {
    btn.addEventListener('click', function () {
        document.querySelector('#form-connection').classList.toggle('hidden')
        document.querySelector('#form-create-account').classList.toggle('hidden')
})
})

