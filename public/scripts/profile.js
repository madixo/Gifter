/** @type {HTMLDivElement} */
const profile = document.querySelector('#profile')
/** @type {HTMLDivElement} */
const content = profile.querySelector('.profile-content');
/** @type {HTMLDivElement} */
const modal = profile.querySelector('.profile-modal');
/** @type {HTMLDivElement} */
const arrow = content.querySelector('.profile-button');

profile.querySelector('.profile-content').addEventListener('click', e => {

    if(!content.classList.contains('active')) {

        arrow.style.rotate = '180deg';
        modal.style.display = 'block';

        e.stopPropagation();

    }else {

        arrow.style.rotate = '';
        modal.style.display = '';

    }

    content.classList.toggle('active');

});

document.addEventListener('click', e => {

    if(content.classList.contains('active')) {

        arrow.style.rotate = '';
        modal.style.display = '';
        content.classList.remove('active');

    }

})