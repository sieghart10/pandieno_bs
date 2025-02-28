// Environment configuration
let cartCount = 0;
let serverIP = '192.168.8.108'; // Default value

// Load environment variables (this should be in your PHP file)
// Note: JavaScript cannot directly read .env files in the browser
// You should expose this through your PHP API or environment configuration

function fetchCartCount() {
    // Use the correct path based on your server configuration
    fetch(`http://${serverIP}/pandieno_bookstore/php/get_cart_count.php`, {
        credentials: 'include', // Include cookies if using sessions
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            cartCount = parseInt(data.count, 10);
            updateCartCountDisplay();
        } else {
            console.error('Failed to fetch cart count:', data.message);
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

function addToCart(bookId) {
    const quantity = document.getElementById('quantity')?.value || 1;

    fetch(`http://${serverIP}/pandieno_bookstore/php/add_to_cart.php`, {
        method: 'POST',
        credentials: 'include', // Include cookies if using sessions
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            book_id: bookId,
            quantity: parseInt(quantity, 10)
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            cartCount += parseInt(quantity, 10);
            updateCartCountDisplay();
            alert('Book added to cart successfully!');
        } else {
            alert(data.message || 'Failed to add book to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add book to cart. Please try again.');
    });
}

function redirectToCheckout(bookId) {
    const quantity = document.getElementById('quantity')?.value || 1;
    const url = `http://${serverIP}/pandieno_bookstore/php/checkout.php?book_id=${encodeURIComponent(bookId)}&quantity=${encodeURIComponent(quantity)}`;
    window.location.href = url;
}

// Initialize cart count when page loads
window.addEventListener('load', fetchCartCount);
