'use strict';

// /**
//  * @param {{id: string,
//  *          name: string,
//  *          access_code: ?string}} props
//  */
// const generateItem = (props) => {

//     const listItemDiv = document.createElement('div');
//     listItemDiv.classList.add('list-item');
//     listItemDiv.dataset['id'] = props.id;

//     if(list.dataset['selectable'] !== undefined) {

//         const itemCheckbox = document.createElement('input');
//         itemCheckbox.type = 'checkbox';
//         itemCheckbox.classList.add('checkbox', 'list-item-select');

//         itemCheckbox.addEventListener('change', () => selectItem(list));

//         listItemDiv.appendChild(itemCheckbox);

//     }

//     const itemContentsDiv = document.createElement('div');
//     itemContentsDiv.classList.add('list-item-contents');

//     const itemNameDiv = document.createElement('div');
//     itemNameDiv.classList.add('list-item-text');
//     itemNameDiv.textContent = props.name;

//     itemContentsDiv.appendChild(itemNameDiv);

//     if(props.access_code) {

//         const itemCodeDiv = document.createElement('div');
//         itemCodeDiv.classList.add('list-item-text');
//         itemCodeDiv.textContent = props.access_code;

//         itemContentsDiv.appendChild(itemCodeDiv);

//     }

//     const itemControlsDiv = document.createElement('div');
//     itemControlsDiv.classList.add('list-item-controls');

//     if(list.dataset['openable'] !== undefined) {

//         const openItem = document.createElement('a');
//         openItem.href = `list?id=${props.id}`;
//         openItem.classList.add('list-item-open');

//         const openItemIcon = document.createElement('i');
//         openItemIcon.classList.add('fa-solid', 'fa-link');

//         openItem.append(openItemIcon, document.createTextNode('Otwórz'));

//         itemControlsDiv.appendChild(openItem);

//     }

//     if(list.dataset['editable'] !== undefined) {

//         const editItem = document.createElement('a');
//         editItem.href = `edit-list?id=${props.id}`;
//         editItem.classList.add('list-item-edit')

//         const editItemIcon = document.createElement('i');
//         editItemIcon.classList.add('fa-regular', 'fa-pen-to-square');

//         editItem.append(editItemIcon, document.createTextNode('Edytuj'));

//         itemControlsDiv.appendChild(editItem);

//     }

//     if(list.dataset['removable'] !== undefined) {

//         const removeItemButton = document.createElement('button');
//         removeItemButton.classList.add('list-item-remove');

//         removeItemButton.addEventListener('click', () => deleteList(list, listItemDiv, list.dataset['endpoint']));

//         const removeItemIcon = document.createElement('i');
//         removeItemIcon.classList.add('fa-regular', 'fa-trash-can');

//         removeItemButton.append(removeItemIcon, document.createTextNode('Usuń'));

//         itemControlsDiv.appendChild(removeItemButton);

//     }

//     listItemDiv.append(itemContentsDiv, itemControlsDiv);

//     return listItemDiv;

// };

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
                data: value
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