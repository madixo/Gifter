const name = document.querySelector('#dashboard .name');

document.querySelector("#change-name form").addEventListener('submit', e => {

    e.preventDefault();

    const input = e.target.querySelector('input[name=name]');

    fetch('list', {

        method: 'UPDATE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            csrfToken: csrfToken,
            id: listId,
            name: input.value
        })

    })
    .then(r => r.json())
    .then(data => {

        if(data['status']) {

            name.textContent = input.value;
            e.target.reset();

        }else {

            displayNotification(data['message'], 'error');

        }

    });

});
