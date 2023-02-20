'use strict';

const defaultHeaders = {
    'Content-Type': 'application/json'
};

const defaultBody = {
    csrfToken: csrfToken
};

/**
 *
 * @param {HTMLDivElement} list
 */
const selectItem = list => {

    const checkbox = list.querySelector('.list-select');
    const deleteButton = list.querySelector('.list-delete');
    const selectedItems = list.querySelectorAll('.list-item-select:checked');

    if(selectedItems.length) {

        deleteButton.disabled = false;

        if(selectedItems.length === list.querySelectorAll('.list-item').length) {

            checkbox.indeterminate = false;
            checkbox.checked = true;

        }else {

            checkbox.indeterminate = true;
            checkbox.checked = true;

        }

    }else {

        deleteButton.disabled = true;
        checkbox.indeterminate = false;
        checkbox.checked = false;

    }

};

/**
 *
 * @param {HTMLDivElement} list
 * @param {HTMLDivElement} item
 * @param {string} endpoint
 */
const deleteList = (list, item, endpoint) => {

    const id = item.dataset['id'];
    const deleteButton = list.querySelector('.list-delete');
    const checkbox = list.querySelector('.list-select');

    fetch(endpoint, {

        method: 'DELETE',
        headers: defaultHeaders,
        body: JSON.stringify({
            ...defaultBody,
            id: id
        })

    })
        .then(response => response.json())
        .then(data => {

            if(data['status']) {

                if(list.querySelectorAll('.list-item-select:checked').length) {

                    checkbox.indeterminate = false;
                    checkbox.checked = false;
                    deleteButton.disabled = true;

                }

                item.remove();

                if(!list.querySelectorAll('.list-item').length) checkbox.disabled = true;

            }

        });

};

const deleteLists = (list, endpoint) => {

    const checkbox = list.querySelector('.list-select');
    const deleteButton = list.querySelector('.list-delete');
    const selectedItems = Array.from(list.querySelectorAll('.list-item')).filter(item => item.querySelector('.list-item-select:checked'));

    if(!selectedItems.length) return;

    fetch(endpoint, {

        method: 'DELETE',
        headers: defaultHeaders,
        body: JSON.stringify({
            ...defaultBody,
            ids: selectedItems.map(item => item.dataset['id'])
        })

    })
        .then(response => response.json())
        .then(data => {

            if(data['status']) {

                if(list.querySelectorAll('.list-item-select:checked').length) {

                    checkbox.indeterminate = false;
                    checkbox.checked = false;
                    deleteButton.disabled = true;

                }

                selectedItems.forEach(item => item.remove());

                if(!list.querySelectorAll('.list-item').length) checkbox.disabled = true;

            }

        });

};

/**
 *
 * @param {HTMLDivElement} list
 * @param {{id: string,
 *          name: string,
 *          access_code: ?string}} props
 */
const appendNewItem = (list, props) => {

    const listItemDiv = document.createElement('div');
    listItemDiv.classList.add('list-item');
    listItemDiv.dataset['id'] = props.id;

    if(list.dataset['selectable'] !== undefined) {

        const itemCheckbox = document.createElement('input');
        itemCheckbox.type = 'checkbox';
        itemCheckbox.classList.add('checkbox', 'list-item-select');

        itemCheckbox.addEventListener('change', () => selectItem(list));

        listItemDiv.appendChild(itemCheckbox);

    }

    const itemNameDiv = document.createElement('div');
    itemNameDiv.classList.add('list-item-name');
    itemNameDiv.textContent = `${props.name} ${props.access_code !== undefined ? `(${props.access_code})` : ''}`;

    const itemControlsDiv = document.createElement('div');
    itemControlsDiv.classList.add('list-item-controls');

    if(list.dataset['openable'] !== undefined) {

        const openItem = document.createElement('a');
        openItem.href = `list?id=${props.id}`;
        openItem.classList.add('list-item-open');

        const openItemIcon = document.createElement('i');
        openItemIcon.classList.add('fa-solid', 'fa-link');

        openItem.append(openItemIcon, document.createTextNode('Otwórz'));

        itemControlsDiv.appendChild(openItem);

    }

    if(list.dataset['editable'] !== undefined) {

        const editItem = document.createElement('a');
        editItem.href = `editList?id=${props.id}`;
        editItem.classList.add('list-item-edit')

        const editItemIcon = document.createElement('i');
        editItemIcon.classList.add('fa-regular', 'fa-pen-to-square');

        editItem.append(editItemIcon, document.createTextNode('Edytuj'));

        itemControlsDiv.appendChild(editItem);

    }

    if(list.dataset['removable'] !== undefined) {

        const removeItemButton = document.createElement('button');
        removeItemButton.classList.add('list-item-remove');

        removeItemButton.addEventListener('click', () => deleteList(list, listItemDiv, list.dataset['endpoint']));

        const removeItemIcon = document.createElement('i');
        removeItemIcon.classList.add('fa-regular', 'fa-trash-can');

        removeItemButton.append(removeItemIcon, document.createTextNode('Usuń'));

        itemControlsDiv.appendChild(removeItemButton);

    }

    listItemDiv.append(itemNameDiv, itemControlsDiv);

    list.querySelector('.list-content').appendChild(listItemDiv);

};

document.querySelectorAll('.list').forEach(list => {

    const checkbox = list.querySelector('.list-select');
    const deleteButton = list.querySelector('.list-delete');
    const addButton = list.querySelector('.list-add');
    const form = list.querySelector('form');
    const formInput = list.querySelector('input[type=text]');
    const endpoint = list.dataset['endpoint'];
    const controls = list.querySelector('.list-controls-buttons')

    checkbox.addEventListener('change', e => {

        const listItemsCheckboxes = list.querySelectorAll('.list-item input[type=checkbox]');

        e.target.indeterminate = false;

        if(e.target.checked) {

            listItemsCheckboxes.forEach(checkbox => checkbox.checked = true);
            deleteButton.disabled = false;

        }else {

            listItemsCheckboxes.forEach(checkbox => checkbox.checked = false);
            deleteButton.disabled = true;

        }

    });

    list.querySelectorAll('.list-item').forEach(item => {

        item.querySelector('.list-item-select').addEventListener('change', () => selectItem(list, item));

        item.querySelector('.list-item-delete').addEventListener('click', () => deleteList(list, item, endpoint));

    });

    deleteButton.addEventListener('click', () => deleteLists(list, endpoint))

    addButton.addEventListener('click', () => {

        addButton.classList.add('hidden');
        form.classList.remove('hidden');
        formInput.focus();

    });

    const hideform = () => {

        addButton.classList.remove('hidden');
        form.classList.add('hidden');

    };

    form.addEventListener('submit', e => {

        e.preventDefault();

        fetch(endpoint, {

            method: 'PUT',
            headers: defaultHeaders,
            body: JSON.stringify({
                ...defaultBody,
                data: formInput.value
            })

        })
            .then(response => response.json())
            .then(data => {

                if(data['status']) {

                    appendNewItem(list, data['list_info']);

                    form.classList.add('hidden');
                    addButton.classList.remove('hidden');
                    if(checkbox.disabled) checkbox.disabled = false;

                }

                form.reset();

            });

    });

    form.addEventListener('keydown', e => e.key === "Escape" ? hideform() : undefined);

    document.addEventListener('click', e => ![...Array.from(form.children), addButton].includes(e.target) && !form.classList.contains('hidden') ? hideform() : undefined);

});