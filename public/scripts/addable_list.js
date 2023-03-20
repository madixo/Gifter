'use strict';

class AddableList {

    /**
     * @param {HTMLDivElement} list
     * @param {{callbacks: {
     *              addItem(value: string): Promise<{id: number, name: string, access_code: ?number}>,
     *              removeItem(id: number): Promise<void>,
     *              removeItems(ids: number): Promise<void>
     *          }
     *         }} init
     */
    constructor(list, init) {

        console.log(init);

        this.options = init;

        this.list = list;
        this.checkbox = list.querySelector('.list-select');
        this.deleteButton = list.querySelector('.list-delete');
        this.addButton = list.querySelector('.list-add');
        this.contents = list.querySelector('.list-contents');
        this.form = list.querySelector('form');
        this.formInput = list.querySelector('input[type=text]');
        this.endpoint = list.dataset['endpoint'];

        this.checkbox.addEventListener('change', e => {

            const listItemsCheckboxes = this.list.querySelectorAll('.list-item input[type=checkbox]');

            e.target.indeterminate = false;

            if(e.target.checked) {

                listItemsCheckboxes.forEach(checkbox => checkbox.checked = true);
                this.deleteButton.disabled = false;

            }else {

                listItemsCheckboxes.forEach(checkbox => checkbox.checked = false);
                this.deleteButton.disabled = true;

            }

        });

        this.list.querySelectorAll('.list-item').forEach(item => {

            item.querySelector('.list-item-select').addEventListener('change', () => this.selectItem(item));

            item.querySelector('.list-item-delete').addEventListener('click', () => this.removeItem(item));

        });

        this.deleteButton.addEventListener('click', () => this.removeItems());

        this.addButton.addEventListener('click', () => {

            this.addButton.classList.add('hidden');
            this.form.classList.remove('hidden');
            this.formInput.focus();

        });

        this.form.addEventListener('submit', e => {

            e.preventDefault();

            this.addItem();

        });

        this.form.addEventListener('keydown', e => e.key === 'Escape' ? this.hideForm() : undefined);

        document.addEventListener('click', e => ![...Array.from(this.form.children), this.addButton].includes(e.target) && !this.form.classList.contains('hidden') ? this.hideForm() : undefined);

    }

    addItem() {

        this.options.callbacks.addItem(this.formInput.value)
            .then(data => {

                this.contents.appendChild(this.generateItem(data));

                this.form.classList.add('hidden');
                this.addButton.classList.remove('hidden');
                if(this.checkbox.disabled) this.checkbox.disabled = false;

                this.form.reset();

            }, err => {

                displayNotification(err, 'error');

            });

    }

    /**
     * @param {HTMLDivElement} item
     */
    removeItem(item) {

        this.options.callbacks.removeItem(item.dataset['id'])
            .then(() => {

                if(this.list.querySelectorAll('.list-item-select:checked').length) {

                    this.checkbox.indeterminate = false;
                    this.checkbox.checked = false;
                    this.deleteButton.disabled = true;

                }

                item.remove();

                if(!this.list.querySelectorAll('.list-item').length) this.checkbox.disabled = true;

            }, err => {

                displayNotification(err, 'error');

            });

    }

    removeItems() {

        const selectedItems = Array.from(this.list.querySelectorAll('.list-item')).filter(item => item.querySelector('.list-item-select:checked'));

        if(!selectedItems.length) return;

        this.options.callbacks.removeItems(selectedItems.map(item => item.dataset['id']))
            .then(() => {

                if(this.list.querySelectorAll('.list-item-select:checked').length) {

                    this.checkbox.indeterminate = false;
                    this.checkbox.checked = false;
                    this.deleteButton.disabled = true;

                }

                selectedItems.forEach(item => item.remove());

                if(!this.list.querySelectorAll('.list-item').length) this.checkbox.disabled = true;

            }, err => {

                displayNotification(err, 'error');

            });

    }

    /**
     * @param {HTMLDivElement}
     */
    selectItem(item) {

        const selectedItems = this.list.querySelectorAll('.list-item-select:checked')

        if(selectedItems.length) {

            this.deleteButton.disabled = false;

            if(selectedItems.length === this.list.querySelectorAll('.list-item').length) {

                this.checkbox.indeterminate = false;
                this.checkbox.checked = true;

            }else {

                this.checkbox.indeterminate = true;
                this.checkbox.checked = true;

            }

        }else {

            this.deleteButton.disabled = true;
            this.checkbox.indeterminate = false;
            this.checkbox.checked = false;

        }

    }

    hideForm() {

        this.addButton.classList.remove('hidden');
        this.form.classList.add('hidden');

    }

    /**
     * @param {{id: string,
     *          name: string,
     *          access_code: ?string}} props
     */
    generateItem(props) {

        const listItemDiv = document.createElement('div');
        listItemDiv.classList.add('list-item');
        listItemDiv.dataset['id'] = props.id;

        if(this.list.dataset['selectable'] !== undefined) {

            const itemCheckbox = document.createElement('input');
            itemCheckbox.type = 'checkbox';
            itemCheckbox.classList.add('checkbox', 'list-item-select');

            itemCheckbox.addEventListener('change', () => this.selectItem(listItemDiv));

            listItemDiv.appendChild(itemCheckbox);

        }

        const itemContentsDiv = document.createElement('div');
        itemContentsDiv.classList.add('list-item-contents');

        const itemNameDiv = document.createElement('div');
        itemNameDiv.classList.add('list-item-text');
        itemNameDiv.textContent = props.name;

        itemContentsDiv.appendChild(itemNameDiv);

        if(props.access_code) {

            const itemCodeDiv = document.createElement('div');
            itemCodeDiv.classList.add('list-item-text');
            itemCodeDiv.textContent = props.access_code;

            itemContentsDiv.appendChild(itemCodeDiv);

        }

        const itemControlsDiv = document.createElement('div');
        itemControlsDiv.classList.add('list-item-controls');

        if(this.list.dataset['openable'] !== undefined) {

            const openItem = document.createElement('a');
            openItem.href = `list?id=${props.id}`;
            openItem.classList.add('list-item-open');

            const openItemIcon = document.createElement('i');
            openItemIcon.classList.add('fa-solid', 'fa-link');

            openItem.append(openItemIcon, document.createTextNode('Otwórz'));

            itemControlsDiv.appendChild(openItem);

        }

        if(this.list.dataset['editable'] !== undefined) {

            const editItem = document.createElement('a');
            editItem.href = `edit-list?id=${props.id}`;
            editItem.classList.add('list-item-edit')

            const editItemIcon = document.createElement('i');
            editItemIcon.classList.add('fa-regular', 'fa-pen-to-square');

            editItem.append(editItemIcon, document.createTextNode('Edytuj'));

            itemControlsDiv.appendChild(editItem);

        }

        if(this.list.dataset['removable'] !== undefined) {

            const removeItemButton = document.createElement('button');
            removeItemButton.classList.add('list-item-remove');

            removeItemButton.addEventListener('click', () => this.removeItem(listItemDiv));

            const removeItemIcon = document.createElement('i');
            removeItemIcon.classList.add('fa-regular', 'fa-trash-can');

            removeItemButton.append(removeItemIcon, document.createTextNode('Usuń'));

            itemControlsDiv.appendChild(removeItemButton);

        }

        listItemDiv.append(itemContentsDiv, itemControlsDiv);

        return listItemDiv;

   }

}