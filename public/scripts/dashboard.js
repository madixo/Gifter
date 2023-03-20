'use strict';

const defaultHeaders = {
    'Content-Type': 'application/json'
};

const defaultBody = {
    csrfToken: csrfToken
};

/**
 * @param {string} endpoint
 * @param {string} value
 * @returns {Promise<{id: number, name: string, access_code: number}>}
 */
const addList = (endpoint, value) => {

    return new Promise((res, rej) => {

        fetch(endpoint, {

            method: 'PUT',
            headers: defaultHeaders,
            body: JSON.stringify({
                ...defaultBody,
                name: value
            })

        })
            .then(response => response.json(), rej)
            .then(data => {

                if(data['status']) {

                    res(data['list_info']);

                }else {

                    rej(data['message']);

                }

            }, rej);

    });

};

/**
 * @param {string} endpoint
 * @param {number} id
 * @returns {Promise<void>}
 */
const removeList = (endpoint, id) => {

    return new Promise((res, rej) => {

        fetch(endpoint, {

            method: 'DELETE',
            headers: defaultHeaders,
            body: JSON.stringify({
                ...defaultBody,
                id: id
            })

        })
            .then(response => response.json(), rej)
            .then(data => {

                if(data['status']) {

                    res();

                }else {

                    rej(data['message']);

                }

            }, rej);

    });

}

/**
 * @param {string} endpoint
 * @param {number} ids
 * @returns {Promise<void>}
 */
const removeLists = (endpoint, ids) => {

    return new Promise((res, rej) => {

        fetch(endpoint, {

            method: 'DELETE',
            headers: defaultHeaders,
            body: JSON.stringify({
                ...defaultBody,
                ids: ids
            })

        })
            .then(response => response.json(), rej)
            .then(data => {

                if(data['status']) {

                    res();

                }else {

                    rej(data['message']);

                }

            }, rej);

    });

}

new AddableList(document.querySelector('#my-lists'), {
    callbacks: {
        addItem: (value) => {

            return addList('/list', value);

        },
        removeItem: (id) => {

            return removeList('/list', id);

        },
        removeItems: (ids) => {

            return removeLists('/list', ids);

        }
    }
});

new AddableList(document.querySelector('#other-lists'), {
    callbacks: {
        addItem: (value) => {

            return addList('/contribution', value);

        },
        removeItem: (id) => {

            return removeList('/contribution', id);

        },
        removeItems: (ids) => {

            return removeLists('/contribution', ids);

        }
    }
});