let cartCount = 0;

function fetchCartCount() {
    fetch('http://localhost:3000/php/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartCount = parseInt(data.count, 10);
                updateCartCountDisplay();
            } else {
                console.error('Failed to fetch cart count.');
                updateCartCountDisplay();
            }
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
            updateCartCountDisplay();
        });
}

function updateCartCountDisplay() {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount > 0 ? cartCount : '';
        cartCountElement.style.display = cartCount > 0 ? 'inline' : 'none';
    }
}

window.onload = fetchCartCount;

function addToCart(bookId) { 
    console.log("Book added to cart:", bookId);
    const quantity = document.getElementById('quantity').value;

    fetch('http://localhost:3000/php/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            book_id: bookId,
            quantity: quantity
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log('Raw Response:', data);
        return JSON.parse(data);
    })
    .then(data => {
        if (data.success) {
            cartCount += parseInt(quantity);
            updateCartCountDisplay();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function redirectToCheckout(bookId) {
    const quantity = document.getElementById('quantity').value;
    const url = `http://localhost:3000/php/checkout.php?book_id=${bookId}&quantity=${quantity}`;
    window.location.href = url;
}
