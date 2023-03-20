'use strict';

/**
 * @type {NodeListOf<HTMLInputElement>} inputs
 */
const inputs = document.querySelectorAll('input[type=password]');

inputs.forEach(e => e.addEventListener('input', () => {

    if(inputs[0].value == inputs[1].value) inputs[1].classList.remove('invalid');
    else inputs[1].classList.add('invalid');

}));

document.querySelector("form").addEventListener('submit', event => {

    if(inputs[0].value != inputs[1].value) event.preventDefault();

});