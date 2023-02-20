'use strict';

document.querySelectorAll('.message').forEach(message => {
    message.querySelector('.close-button').addEventListener('click', () => {
        message.remove();
    });
});