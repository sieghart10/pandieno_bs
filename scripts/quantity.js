// quantity.js
function increaseQuantity(elementId) {
    const quantityElement = document.getElementById(elementId);
    const currentQuantity = parseInt(quantityElement.textContent);
    const newQuantity = currentQuantity + 1;

    updateCartItemQuantity(elementId, newQuantity);
    quantityElement.textContent = newQuantity;
    updateTotalPrice(quantityElement);
    updateCartItemCountDisplay();
    updateTotalItemsAndAmount();
}

function decreaseQuantity(elementId) {
    const quantityElement = document.getElementById(elementId);
    const currentQuantity = parseInt(quantityElement.textContent);

    if (currentQuantity > 1) {
        const newQuantity = currentQuantity - 1;
        updateCartItemQuantity(elementId, newQuantity);
        quantityElement.textContent = newQuantity;
        updateTotalPrice(quantityElement);
        updateCartItemCountDisplay();
        updateTotalItemsAndAmount();
    } else {
        const cartItemId = elementId.split('-').pop();
        deleteItem(cartItemId);
    }
}


function deleteItem(cartItemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch('http://localhost:3000/php/delete_cart_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ cart_item_id: cartItemId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`checkbox-item-${cartItemId}`).closest('.items').remove();
                alert('Item deleted successfully');
                updateCartItemCountDisplay();
                updateTotalItemsAndAmount();
            } else {
                alert('Error deleting item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function updateTotalPrice(quantityElement) {
    const itemContainer = quantityElement.closest('.items');
    const price = parseFloat(itemContainer.querySelector('.item-price').textContent.replace('₱', '').replace(',', ''));
    const quantity = parseInt(quantityElement.textContent);
    const totalPriceElement = itemContainer.querySelector('.item-totalprice');
    totalPriceElement.textContent = '₱' + (price * quantity).toFixed(2);
}

// Update cart item count display
function updateCartItemCountDisplay() {
    fetch('http://localhost:3000/php/get_cart_count.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        const cartCountElement = document.querySelector('.cart-count');
        if (data.count > 0) {
            cartCountElement.textContent = data.count;
            cartCountElement.style.display = 'inline';
        } else {
            cartCountElement.style.display = 'none';
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateCartItemQuantity(elementId, newQuantity) {
    const cartItemId = elementId.split('-').pop();
    fetch('http://localhost:3000/php/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ cart_item_id: cartItemId, quantity: newQuantity })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating quantity:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

document.querySelector('.select-all').textContent = 'Select All (0)';

function updateTotalItemsAndAmount() {
    const itemCheckboxes = document.querySelectorAll('.item-select');
    let totalQuantity = 0;
    let totalAmount = 0;

    itemCheckboxes.forEach(checkbox => {
        const quantity = parseInt(document.getElementById(`item-quantity-${checkbox.id.split('-').pop()}`).textContent);
        const price = parseFloat(checkbox.closest('.items').querySelector('.item-price').textContent.replace('₱', '').replace(',', ''));

        const totalPriceElement = checkbox.closest('.items').querySelector('.item-totalprice');
        totalPriceElement.textContent = '₱' + (price * quantity).toFixed(2);

        if (checkbox.checked) {
            totalQuantity += quantity;
            totalAmount += price * quantity;
        }
    });

    document.querySelector('.select-all').textContent = `Select All (${totalQuantity})`;
    document.querySelector('.totalitems').textContent = `Total (${totalQuantity} Item/s): ₱${totalAmount.toFixed(2)}`;
}


document.getElementById('checkbox-foot').addEventListener('change', function () {
    const itemCheckboxes = document.querySelectorAll('.item-select');
    itemCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
    updateTotalItemsAndAmount();
});

document.querySelectorAll('.item-select').forEach(checkbox => {
    checkbox.addEventListener('change', updateTotalItemsAndAmount);
});

document.querySelector('.delete-all').addEventListener('click', function() {
    const itemCheckboxes = document.querySelectorAll('.item-select:checked');
    const itemIds = Array.from(itemCheckboxes).map(checkbox => checkbox.id.split('-').pop());

    if (itemIds.length === 0) {
        alert('No items selected for deletion.');
        return;
    }

    if (confirm('Are you sure you want to delete the selected items?')) {
        Promise.all(itemIds.map(cartItemId => {
            return fetch('http://localhost:3000/php/delete_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cart_item_id: cartItemId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Error deleting item:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }))
        .then(() => {
            itemCheckboxes.forEach(checkbox => {
                checkbox.closest('.items').remove();
            });
            updateCartItemCountDisplay();
            updateTotalItemsAndAmount();
            alert('Selected items deleted successfully.');
        });
    }
});

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('checkbox-foot');
    const itemCheckboxes = document.querySelectorAll('.item-select');
    const isChecked = selectAllCheckbox.checked;

    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });

    updateTotalItemsAndAmount();
}

document.addEventListener('DOMContentLoaded', () => {
    const itemCheckboxes = document.querySelectorAll('.item-select');
    const selectAllCheckbox = document.getElementById('checkbox-foot');
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
    }

    updateTotalItemsAndAmount(); 

    const checkbox = document.querySelector('input[type="checkbox"]');

    window.addEventListener('beforeunload', () => {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
    });

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', () => {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
        });
    }
});

const selectAllCheckbox = document.getElementById('checkbox-foot');
if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
        const itemCheckboxes = document.querySelectorAll('.item-select');
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateTotalItemsAndAmount();
    });
}

document.querySelectorAll('.item-select').forEach(checkbox => {
    checkbox.addEventListener('change', updateTotalItemsAndAmount);
});

updateTotalItemsAndAmount();