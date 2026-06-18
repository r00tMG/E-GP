    let kiloIndex = 1;
     let specialIndex = 1;

     $('#add-kilo').click(function() {

     $('#kilo-items').append(`
        <div class="row mb-3 kilo-item">
            <div class="col-md-6">
                <input type="text"
                       name="kilos[${kiloIndex}][item_name]"
                       class="form-control">
            </div>

            <div class="col-md-4">
                <input type="number"
                       step="0.1"
                       min="0"
                       name="kilos[${kiloIndex}][weight]"
                       class="form-control">
            </div>

            <div class="col-md-2">
                <button type="button"
                        class="btn btn-danger remove-kilo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
  <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
</svg>
                </button>
            </div>
        </div>
    `);
     kiloIndex++;
     calculerTotal();
 });

     $(document).on('click', '.remove-kilo', function() {
     $(this).closest('.kilo-item').remove();
         calculerTotal();
 });

     $('#add-special').click(function() {

     $('#special-items').append(`
        <div class="row mb-3 special-item">
            <div class="col-md-6">
                <input type="text"
                       name="specials[${specialIndex}][item_name]"
                       class="form-control">
            </div>

            <div class="col-md-4">
                <input type="number"
                       min="1"
                       name="specials[${specialIndex}][quantity]"
                       class="form-control">
            </div>

            <div class="col-md-2">
                <button type="button"
                        class="btn btn-danger remove-special text-white">
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
  <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
</svg>
                </button>
            </div>
        </div>
    `);

     specialIndex++;
         calculerTotal();
 });

    $(document).on('input', 'input[name*="[weight]"]', function () {

        calculerTotal();
    });

    $(document).on('input', 'input[name*="[quantity]"]', function () {
        calculerTotal();
    });

    function calculerTotal() {

        let prixKilo = Number($('#prix-kilo').val()) || 0;
        let prixPiece = Number($('#prix-piece').val()) || 0;

        let totalKg = 0;
        let totalPieces = 0;

        document.querySelectorAll('input[name*="[weight]"]').forEach(el => {
            totalKg += Number(el.value) || 0;
        });

        document.querySelectorAll('input[name*="[quantity]"]').forEach(el => {
            totalPieces += Number(el.value) || 0;
        });

        let total = (totalKg * prixKilo) + (totalPieces * prixPiece);

        document.getElementById('total-estime').innerText =
            total.toLocaleString('fr-FR') + ' FCFA';
    }


    /*$(document).on('click', '.remove-special', function () {
        $(this).closest('.special-item').remove();
    });


    let ingredientIndex = 1;
    document.getElementById('add-ingredient').addEventListener('click', function () {
        const container = document.getElementById('ingredients-container');
        const newIngredient = document.createElement('div');
        newIngredient.classList.add('ingredient-item');
        newIngredient.innerHTML = `
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="ingredients[${ingredientIndex}][name]" placeholder="Nom de l'ingrédient" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="ingredients[${ingredientIndex}][quantity]" placeholder="Quantité" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="ingredients[${ingredientIndex}][metric]" placeholder="Metric" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="ingredients[${ingredientIndex}][calories]" placeholder="Calories" required>
                </div>
                <div class="col-md-2">
                    <div type="button" class="btn btn-sm btn-danger text-light remove-ingredient">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1 bi bi-x-circle" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        Annuler
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newIngredient);
        ingredientIndex++;
    });
    document.getElementById('ingredients-container').addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-ingredient')) {
            e.target.closest('.ingredient-item').remove();
        }
    });*/




