//Selection des champs du formulaire pour autocompletion du champ prixTTC
let preTaxPriceField = document.querySelector('#products_pre_tax_price');
let vatField = document.querySelector('#products_TVA');

//Chargement page web
window.onload = () => {
    //Ajout d'un écouteur d'événement pour les champs prixHT et TVA
    preTaxPriceField.addEventListener('keyup', fillTaxIncludedPriceField);
    vatField.addEventListener('keyup', fillTaxIncludedPriceField);
}

//Function qui remplie le champ prixTTC en fonction de la tva et du prixHT
function fillTaxIncludedPriceField() {
    //Sélection du champs prixTTC
    let taxIncludedPriceField = document.querySelector('#products_tax_included_price');
    //Remplissage du champs
    taxIncludedPriceField.value = preTaxPriceField.value * ( vatField.value/100 + 1  );
}