let cartCount = 0; // Initialize cart count variable

// Function to fetch the initial cart count
function fetchCartCount() {
    fetch('http://localhost:3000/php/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartCount = parseInt(data.count, 10); // Convert to integer using parseInt
                updateCartCountDisplay(); // Update the display
            } else {
                console.error('Failed to fetch cart count.');
                updateCartCountDisplay(); // Ensure display updates even if fetching fails
            }
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
            updateCartCountDisplay(); // Ensure display updates even if there's an error
        });
}

// Function to update the cart count display
function updateCartCountDisplay() {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount > 0 ? cartCount : ''; // Clear the text if cart is empty
        cartCountElement.style.display = cartCount > 0 ? 'inline' : 'none'; // Show or hide the count
    }
}

// Call fetchCartCount on page load
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
    .then(response => response.text()) // Change to response.text() to read the raw text
    .then(data => {
        console.log('Raw Response:', data); // Log the raw response
        return JSON.parse(data); // Manually parse the JSON
    })
    .then(data => {
        if (data.success) {
            cartCount += parseInt(quantity); // Increment cart count by the quantity added
            updateCartCountDisplay(); // Update the cart count in the UI
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}