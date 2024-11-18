// userProfile.js
document.addEventListener('DOMContentLoaded', () => {
    const profileLink = document.getElementById('profile-link');
    const addressLink = document.getElementById('address-link');
    const ordersLink = document.getElementById('orders-link');
    const contentSection = document.getElementById('content-section');

    const loadContent = async (url) => {
        try {
            const response = await fetch(url);
            const contentType = response.headers.get('content-type');
            
            // Log the raw response content for debugging
            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Invalid content type:', contentType);
                throw new Error(`Invalid response type: ${contentType}. Expected JSON.`);
            }
    
            const data = JSON.parse(responseText);  // Manually parse the JSON
            console.log(data);
            
            if (data.error) {
                contentSection.innerHTML = `
                    <div class="error-message">
                        <h3>Error</h3>
                        <p>${data.error}</p>
                    </div>
                `;
                return;
            }

            let content = '';

            if (url.includes('get_user_details.php')) {
                // Display user details
                content = `
                    <h2>User Profile</h2>
                    <div class="user-details">
                        <p><strong>Username:</strong> ${data.username}</p>
                        <p><strong>Full Name:</strong> ${data.first_name} ${data.middle_name} ${data.last_name}</p>
                        <p><strong>Email:</strong> ${data.email}</p>
                        <p><strong>Gender:</strong> ${data.gender}</p>
                        <p><strong>Birthday:</strong> ${new Date(data.birthday).toLocaleDateString()}</p>
                    </div>
                `;
            }

            if (url.includes('get_user_address.php')) {
                // Display address details
                content += `
                    <h2>Address</h2>
                    <div class="user-address">
                        <p><strong>House No:</strong> ${data.house_no}</p>
                        <p><strong>Street:</strong> ${data.street}</p>
                        <p><strong>Barangay:</strong> ${data.barangay}</p>
                        <p><strong>City:</strong> ${data.city}</p>
                        <p><strong>Province:</strong> ${data.province}</p>
                    </div>
                `;
            }
            
            if (url.includes('get_user_orders.php')) {
                if (Array.isArray(data) && data.length > 0) {
                    content = `
                        <h2>My Orders</h2>
                        <div class="orders-container">
                    `;
                    
                    data.forEach(order => {
                        content += `
                            <div class="order-card">
                                <div class="order-header">
                                    <h3>Order #${order.order_id}</h3>
                                    <span class="status ${order.order_status.toLowerCase()}">${order.order_status}</span>
                                </div>
                                <div class="order-details">
                                    <img src="${order.cover_image}" alt="${order.title}" class="book-cover" onerror="this.src='../images/placeholder.png'"/>
                                    <div class="order-info">
                                        <p class="book-title">${order.title}</p>
                                        <p>Price: $${parseFloat(order.price).toFixed(2)}</p>
                                        <p>Quantity: ${order.quantity}</p>
                                        <p>Date: ${new Date(order.date).toLocaleDateString()}</p>
                                        <p>Payment: ${order.payment_method}</p>
                                    </div>
                                </div>
                                ${order.order_status === 'pending' ? `
                                    <button class="cancel-order" data-order-id="${order.order_id}">
                                        Cancel Order
                                    </button>
                                ` : ''}
                                ${order.order_status === 'cancelled' ? `
                                    <button class="delete-order" data-order-id="${order.order_id}">
                                        Delete Order
                                    </button>
                                ` : ''}
                            </div>
                        `;
                    });
                    
                    content += '</div>';
                } else {
                    content = '<h2>My Orders</h2><p>No orders found.</p>';
                }
            }

            contentSection.innerHTML = content;

            // Add event listeners for cancel buttons
            if (url.includes('get_user_orders.php')) {
                document.querySelectorAll('.cancel-order').forEach(button => {
                    button.addEventListener('click', handleCancelOrder);
                });
            }

            if (url.includes('get_user_orders.php')) {
                document.querySelectorAll('.delete-order').forEach(button => {
                    button.addEventListener('click', handleDeleteOrder);
                });
            }

        } catch (error) {
            console.error('Error details:', error);
            contentSection.innerHTML = `
                <div class="error-message">
                    <h3>Error</h3>
                    <p>Failed to load content. Please try again later.</p>
                </div>
            `;
        }
    };

    const handleCancelOrder = async (event) => {
        const orderId = event.target.dataset.orderId;
        if (confirm('Are you sure you want to cancel this order?')) {
            try {
                const response = await fetch('get_user_orders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `cancel_order=true&order_id=${orderId}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Order cancelled successfully!');
                    loadContent('get_user_orders.php');
                } else {
                    alert(data.error || 'Failed to cancel order.');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                alert('Failed to cancel order. Please try again.');
            }
        }
    };

    const handleDeleteOrder = async (event) => {
        const orderId = event.target.dataset.orderId;
        if (confirm('Are you sure you want to delete this order?')) {
            try {
                const response = await fetch('get_user_orders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `delete_order=true&order_id=${orderId}`
                });
                
                const data = await response.text();
                
                if (data.includes("Order deleted successfully")) {
                    alert('Order deleted successfully!');
                    loadContent('get_user_orders.php'); // Reload the orders after deletion
                } else {
                    alert('Failed to delete order.');
                }
            } catch (error) {
                console.error('Error deleting order:', error);
                alert('Failed to delete order. Please try again.');
            }
        }
    };


    // Event listeners for navigation
    profileLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadContent('get_user_details.php');
    });

    addressLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadContent('get_user_address.php');
    });

    ordersLink.addEventListener('click', (e) => {
        e.preventDefault();
        loadContent('get_user_orders.php');
    });
});
