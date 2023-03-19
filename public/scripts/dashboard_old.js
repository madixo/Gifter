'use strict';

/** @type {string[][]} */
let ids = [];

const defaultOptions = {
    headers: {
        'Content-Type': 'application/json'
    }
};

const removeItem = (list, listItem, endPoint) => {

    const id = listItem.dataset['id'];
    const listId = list.id;
    const removeSelectedInput = list.querySelector('.list-remove');
    const selectAllCheckbox = list.querySelector('.list-select');

    fetch(endPoint, {
        method: "DELETE",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            csrfToken: csrfToken,
            id: id
        })
    })
        .then(response => response.json())
        .then(data => {

            if(data['status']) {

                const index = ids[listId].indexOf(id);
                if(index != -1) {

                    ids[listId].splice(index, 1);

                    console.log(ids);

                    if(!ids[listId].length) {

                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                        removeSelectedInput.disabled = true;

                    }

                }

                listItem.remove();

                if(list.querySelectorAll('.list-content .list-item').length == 0) selectAllCheckbox.disabled = true;

            }

        });

};

const checkItem = (list, listItem) => {

    const id = listItem.dataset['id'];
    const listId = list.id;
    const allItems = list.querySelectorAll('.list-content .list-item');
    const checkbox = listItem.querySelector('.list-item-select');
    const removeSelectedInput = list.querySelector('.list-remove');
    const selectAllCheckbox = list.querySelector('.list-select');

    checkbox.checked ?
        ids[listId].push(id) :
        ids[listId].splice(ids[listId].indexOf(id), 1);

    if(ids[listId].length) {

        removeSelectedInput.disabled = false;

        if(ids[listId].length == allItems.length) {

            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;

        }else selectAllCheckbox.indeterminate = true;

    }else {

        removeSelectedInput.disabled = true;
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;

    }

};

const editItem = listItem => {

};

const appendNewItem = (list, id, name, accessCode, editable = false) => {

    const listItemDiv = document.createElement('div');
    listItemDiv.classList.add('list-item');
    listItemDiv.dataset['id'] = id;

    const itemCheckbox = document.createElement('input');
    itemCheckbox.type = 'checkbox';
    itemCheckbox.classList.add('checkbox', 'list-item-select');

    itemCheckbox.addEventListener('change', () => checkItem(list, listItemDiv));

    const itemNameDiv = document.createElement('div');
    itemNameDiv.classList.add('list-item-name');
    itemNameDiv.textContent = `${name} (${accessCode})`;

    const itemControlsDiv = document.createElement('div');
    itemControlsDiv.classList.add('list-item-controls');

    const openItem = document.createElement('a');
    openItem.href = `list?id=${id}`;
    openItem.classList.add('list-item-open');

    const openItemIcon = document.createElement('i');
    openItemIcon.classList.add('fa-solid', 'fa-link');

    openItem.append(openItemIcon, document.createTextNode('Otwórz'));

    itemControlsDiv.appendChild(openItem);

    if(editable) {

        const editItem = document.createElement('a');
        editItem.href = `edit-list?id=${id}`;
        editItem.classList.add('list-item-edit')

        const editItemIcon = document.createElement('i');
        editItemIcon.classList.add('fa-regular', 'fa-pen-to-square');

        editItem.append(editItemIcon, document.createTextNode('Edytuj'));

        itemControlsDiv.appendChild(editItem);

    }

    const removeItemButton = document.createElement('button');
    removeItemButton.classList.add('list-item-remove');

    removeItemButton.addEventListener('click', () => removeItem(list, listItemDiv));

    const removeItemIcon = document.createElement('i');
    removeItemIcon.classList.add('fa-regular', 'fa-trash-can');

    removeItemButton.append(removeItemIcon, document.createTextNode('Usuń'));

    itemControlsDiv.appendChild(removeItemButton);

    listItemDiv.append(itemCheckbox, itemNameDiv, itemControlsDiv);

    list.querySelector('.list-content').appendChild(listItemDiv);

}

document.querySelectorAll('.list').forEach(list => {

    const selectAllCheckbox = list.querySelector('.list-select');
    const removeSelectedButton = list.querySelector('.list-remove');
    const addForm = list.querySelector('form');
    const listAdd = list.querySelector('.list-add');
    const addFormInput = addForm.querySelector('input[type=text]');
    const listId = list.id;

    ids[listId] = [];

    selectAllCheckbox.addEventListener('change', e => {

        const allItemsCheckboxes = list.querySelectorAll('.list-item input[type=checkbox]');

        if(e.target.checked) {

            allItemsCheckboxes.forEach(checkbox => checkbox.checked = true);
            ids[listId] = Array.from(allItemsCheckboxes).map(checkbox => checkbox.parentElement.dataset['id']);
            removeSelectedButton.disabled = false;

        }else {

            allItemsCheckboxes.forEach(checkbox => checkbox.checked = false);
            ids[listId].length = 0;
            removeSelectedButton.disabled = true;

        }

    });

    removeSelectedButton.addEventListener('click', e => {

        // if(e.target.disabled) return;

        fetch('lists', {
            method: "DELETE",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                csrfToken: csrfToken,
                ids: ids[listId]
            })
        })
            .then(response => response.json())
            .then(data => {
                if(data['status']) {

                    list.querySelectorAll('.list-content .list-item:has(input[type=checkbox]:checked)').forEach(element => element.remove());
                    e.target.disabled = true;
                    selectAllCheckbox.checked = selectAllCheckbox.indeterminate = false;

                    if(list.querySelectorAll('.list-content .list-item').length == 0) selectAllCheckbox.disabled = true;

                    ids.length = 0;

                }else alert(data['message']);
            });

    });

    addForm.addEventListener('keydown', e => {

        if(e.key === "Escape") {

            listAdd.classList.remove('hidden');
            addForm.classList.add('hidden');

        }

    });

    addForm.addEventListener('blur', () => {

        listAdd.classList.remove('hidden');
        addForm.classList.add('hidden');

    });

    // guzik pokazujacy forma do dodawania
    listAdd.addEventListener('click', () => {

        listAdd.classList.add('hidden');
        addForm.classList.remove('hidden');
        addFormInput.focus();

    });

    // dodawanie eventow do elementow listy
    list.querySelectorAll('.list-content .list-item').forEach(listItem => {

        listItem.querySelector('input[type=checkbox]').addEventListener('change', () => checkItem(list, listItem));

        listItem.querySelector('.list-item-edit')?.addEventListener('click', () => {});

        listItem.querySelector('.list-item-remove').addEventListener('click', () => removeItem(list, listItem));

    });

});

const myList = document.querySelector('#my-lists');

myList.querySelector('form').addEventListener('submit', e => {

    e.preventDefault();

    const input = e.target.querySelector('input[type=text]');
    const checkbox = myList.querySelector('.list-select');

    fetch('list', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            csrfToken: csrfToken,
            name: input.value
        })
    })
        .then(response => response.json())
        .then(data => {
            if(data['status']) {
                appendNewItem(myList, data['id'], data['name'], data['access_code'], true);
                e.target.reset();
                e.target.classList.add('hidden');
                myList.querySelector('.list-add').classList.remove('hidden');
                if(checkbox.disabled) checkbox.disabled = false;
            }
        });

});

const otherList = document.querySelector('#other-lists');

otherList.querySelector('form').addEventListener('submit', e => {

    e.preventDefault();

    const input = e.target.querySelector('input[type=text]');
    const checkbox = otherList.querySelector('.list-select');

    console.log('others');

});