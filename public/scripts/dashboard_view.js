/**
 * @param {string} content
 * @param {'notification'|'error'} type
 */
function displayNotification(content, type = 'notification') {

    const message = document.createElement('div');
    message.classList.add('message', 'barrel');
    if(type == 'error') message.classList.add('message--error');

    const messageText = document.createTextNode(content);

    const closeWrapper = document.createElement('div');
    closeWrapper.classList.add('close-button');

    const closeButton = document.createElement('i');
    closeButton.classList.add('fa-solid', 'fa-xmark');

    closeWrapper.addEventListener('click', () => message.remove());

    closeWrapper.appendChild(closeButton);

    message.append(messageText, closeWrapper);

    document.querySelector('#notifications').append(message);

}