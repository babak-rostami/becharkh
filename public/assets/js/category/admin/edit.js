function selectCategory(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    if (!selectedOption.value) return;
    const categoryTitle = selectedOption.textContent;
    const id = selectedOption.value;
    const categoriesInput = document.getElementById('related_cats');
    categoriesInput.value += (categoriesInput.value ? ',' : '') + id;
    const span = document.createElement('span');
    span.textContent = categoryTitle;
    span.setAttribute('data-id', id);
    span.classList.add('badge', 'badge-dark');
    span.style.cursor = 'pointer';
    span.style.marginRight = '10px';
    span.onclick = function () {
        removeCategory(id, span);
    };
    document.getElementById('rcats').appendChild(span);
    selectedOption.remove();
    selectElement.selectedIndex = 0;
}

function removeCategory(id, span) {
    span.remove();
    const categoriesInput = document.getElementById('related_cats');
    const ids = categoriesInput.value.split(',').filter(catId => catId !== id);
    categoriesInput.value = ids.join(',');
    const select = document.getElementById('categorySelect');
    const option = document.createElement('option');
    option.id = id;
    option.value = id;
    option.textContent = span.textContent;
    select.appendChild(option);
}
const editors = document.querySelectorAll('.ckeditor');
editors.forEach(editor => {
    ClassicEditor.create(editor, {
        language: "fa",
    });
});