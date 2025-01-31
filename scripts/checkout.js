let totalItems = 0;
let totalPrice = 0;

document.addEventListener('DOMContentLoaded', function() {
    initializeCheckout();
});

function initializeCheckout() {
    const checkboxes = document.querySelectorAll('.item-select');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotalPrice);
    });

    const selectAllCheckbox = document.querySelector('.select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', handleSelectAll);
    }

    updateTotalPrice();
}

function handleSelectAll(event) {
    const checkboxes = document.querySelectorAll('.item-select');
    checkboxes.forEach(checkbox => {
        checkbox.checked = event.target.checked;
    });
    updateTotalPrice();
}

function updateTotalPrice() {
    const selectedItems = document.querySelectorAll('.item-select:checked');
    totalPrice = 0;
    totalItems = selectedItems.length;

    selectedItems.forEach(checkbox => {
        const itemContainer = checkbox.closest('.items');
        const priceText = itemContainer.querySelector('.item-totalprice').textContent;
        const price = parseFloat(priceText.replace('₱', '').replace(',', ''));
        totalPrice += price;
    });

    updateDisplayElements();
}

function updateDisplayElements() {
    const totalDisplay = document.querySelector('.totalitems');
    if (totalDisplay) {
        totalDisplay.textContent = `Total (${totalItems} Item${totalItems !== 1 ? 's' : ''}): ₱${totalPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    const selectAllLabel = document.querySelector('.select-all');
    if (selectAllLabel) {
        selectAllLabel.textContent = `Select All (${totalItems})`;
    }
}

function checkoutSelectedItems() {
    const selectedItems = document.querySelectorAll('.item-select:checked');
    if (selectedItems.length === 0) {
        alert('Please select at least one item to checkout');
        return;
    }
    
    const itemIds = Array.from(selectedItems).map(checkbox => 
        checkbox.id.replace('checkbox-item-', '')
    );
    
    window.location.href = `checkout.php?items=${JSON.stringify(itemIds)}`;
}

function processCheckout(items) {
    fetch('process_checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ items })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.href = `checkout.php?order_id=${data.order_id}`;
        } else {
            throw new Error(data.message || 'Checkout failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during checkout: ' + error.message);
    });
}