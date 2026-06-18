
const apiKeyContainer = document.getElementById('api-key');
const addItemButton = document.getElementById('add-item');

addItemButton.addEventListener('click', function(e) {
    e.preventDefault()
    const newRow = document.createElement('div');
    newRow.classList.add('row', 'mt-2', 'align-items-center');

    const newKeyDiv = document.createElement('div');
    newKeyDiv.classList.add('col-md-5');
    const newKeyInput = document.createElement('input');
    newKeyInput.classList.add('form-control');
    newKeyInput.setAttribute('placeholder', 'Key');
    newKeyDiv.appendChild(newKeyInput);

    const newValueDiv = document.createElement('div');
    newValueDiv.classList.add('col-md-5');
    const newValueInput = document.createElement('input');
    newValueInput.classList.add('form-control');
    newValueInput.setAttribute('placeholder', 'Value');
    newValueDiv.appendChild(newValueInput);

    const removeButtonDiv = document.createElement('div');
    removeButtonDiv.classList.add('col-md-2', 'd-flex', 'justify-content-center');
    const removeButton = document.createElement('button');
    removeButton.classList.add('btn', 'btn-danger');
    removeButton.textContent = 'Annuler';
    removeButton.addEventListener('click', function() {
        apiKeyContainer.removeChild(newRow);
    });
    removeButtonDiv.appendChild(removeButton);

    newRow.appendChild(newKeyDiv);
    newRow.appendChild(newValueDiv);
    newRow.appendChild(removeButtonDiv);

    apiKeyContainer.appendChild(newRow);
});
