// quantity.js
function increaseQuantity(elementId) {
    const quantityElement = document.getElementById(elementId);
    const currentQuantity = parseInt(quantityElement.textContent);
    const newQuantity = currentQuantity + 1;

    updateCartItemQuantity(elementId, newQuantity);
    quantityElement.textContent = newQuantity;
    updateTotalPrice(quantityElement);
    updateCartItemCountDisplay(); // Update cart count
    updateTotalItemsAndAmount(); // Update total items and amount
}

function decreaseQuantity(elementId) {
    const quantityElement = document.getElementById(elementId);
    const currentQuantity = parseInt(quantityElement.textContent);

    if (currentQuantity > 1) {
        const newQuantity = currentQuantity - 1;
        updateCartItemQuantity(elementId, newQuantity);
        quantityElement.textContent = newQuantity;
        updateTotalPrice(quantityElement);
        updateCartItemCountDisplay(); // Update cart count
        updateTotalItemsAndAmount(); // Update total items and amount
    } else {
        const cartItemId = elementId.split('-').pop();
        deleteItem(cartItemId); // Call deleteItem if quantity is 1
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
                updateCartItemCountDisplay(); // Update cart count
                updateTotalItemsAndAmount(); // Update total items and amount
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
    const itemContainer = quantityElement.closest('.items'); // Adjust selector if needed
    const price = parseFloat(itemContainer.querySelector('.item-price').textContent.replace('₱', ''));
    const quantity = parseInt(quantityElement.textContent);
    const totalPriceElement = itemContainer.querySelector('.item-totalprice');
    totalPriceElement.textContent = '₱' + (price * quantity).toFixed(2);
}

// Update cart item count display
function updateCartItemCountDisplay() {
    fetch('http://localhost:3000/php/get_cart_count.php', { // PHP script to fetch cart item count
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

// Set select-all label initially to 0
document.querySelector('.select-all').textContent = 'Select All (0)';

// Function to update total quantity and amount based on selected items
function updateTotalItemsAndAmount() {
    const itemCheckboxes = document.querySelectorAll('.item-select');
    let totalQuantity = 0;
    let totalAmount = 0;

    itemCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const quantity = parseInt(document.getElementById(`item-quantity-${checkbox.id.split('-').pop()}`).textContent);
            const price = parseFloat(checkbox.closest('.items').querySelector('.item-price').textContent.replace('₱', '').replace(',', ''));
            
            totalQuantity += quantity;
            totalAmount += price * quantity;
        }
    });

    // Update Select All label with the count of checked items
    document.querySelector('.select-all').textContent = `Select All (${totalQuantity})`;
    document.querySelector('.totalitems').textContent = `Total (${totalQuantity} Item/s): ₱ ${totalAmount.toFixed(2)}`;
}

// Toggle all items' checkboxes and update the total when "Select All" is checked
document.getElementById('checkbox-foot').addEventListener('change', function () {
    const itemCheckboxes = document.querySelectorAll('.item-select');
    itemCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
    updateTotalItemsAndAmount();
});

// Update the total whenever an individual item's checkbox is toggled
document.querySelectorAll('.item-select').forEach(checkbox => {
    checkbox.addEventListener('change', updateTotalItemsAndAmount);
});

// Function to delete all selected items
document.querySelector('.delete-all').addEventListener('click', function() {
    const itemCheckboxes = document.querySelectorAll('.item-select:checked'); // Select all checked items
    const itemIds = Array.from(itemCheckboxes).map(checkbox => checkbox.id.split('-').pop()); // Extract item IDs

    if (itemIds.length === 0) {
        alert('No items selected for deletion.');
        return;
    }

    if (confirm('Are you sure you want to delete the selected items?')) {
        // Perform deletion for each selected item
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
            // After all deletions, update the UI
            itemCheckboxes.forEach(checkbox => {
                checkbox.closest('.items').remove(); // Remove item from the UI
            });
            updateCartItemCountDisplay(); // Update cart count
            updateTotalItemsAndAmount(); // Update total items and amount
            alert('Selected items deleted successfully.');
        });
    }
});

// Update the total quantity and amount display whenever the page loads
updateTotalItemsAndAmount();