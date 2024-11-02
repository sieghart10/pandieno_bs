window.increaseQuantity = function(id) {
    changeQuantity(id, 1);
}

window.decreaseQuantity = function(id) {
    changeQuantity(id, -1);
}

function changeQuantity(id, delta) {
    const quantityDisplay = document.getElementById(id);
    let currentQuantity = parseInt(quantityDisplay.innerText);
    let newQuantity = currentQuantity + delta;

    const min = 1;
    const max = parseInt(quantityDisplay.getAttribute('data-max'));
    console.log(`Max quantity: ${max}`);

    console.log(`Current: ${currentQuantity}, New: ${newQuantity}, Min: ${min}, Max: ${max}`);

    if (newQuantity >= min && newQuantity <= max) {
        quantityDisplay.innerText = newQuantity;
        console.log(`Updated quantity to: ${newQuantity}`);
    } else {
        console.log("Quantity out of bounds.");
    }
}

