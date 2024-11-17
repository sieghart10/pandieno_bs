document.addEventListener('DOMContentLoaded', () => {
    const profileLink = document.getElementById('profile-link');
    const addressLink = document.getElementById('address-link');
    const ordersLink = document.getElementById('orders-link');
    const contentSection = document.getElementById('content-section');

    const loadContent = (url) => {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    contentSection.innerHTML = `<p>${data.error}</p>`;
                } else {
                    let content = '';
                    if (url.includes('get_user_details.php')) {
                        content = `
                            <h2>Profile</h2>
                            <p><strong>Username:</strong> ${data.username}</p>
                            <p><strong>Name:</strong> ${data.first_name} ${data.middle_name || ''} ${data.last_name}</p>
                            <p><strong>Email:</strong> ${data.email}</p>
                            <p><strong>Gender:</strong> ${data.gender}</p>
                            <p><strong>Date of Birth:</strong> ${new Date(data.birthday).toLocaleDateString()}</p>
                        `;
                    } else if (url.includes('get_user_address.php')) {
                        content = `
                            <h2>Address</h2>
                            <p><strong>House No:</strong> ${data.house_no}</p>
                            <p><strong>Street:</strong> ${data.street}</p>
                            <p><strong>Barangay:</strong> ${data.barangay}</p>
                            <p><strong>City:</strong> ${data.city}</p>
                            <p><strong>Province:</strong> ${data.province}</p>
                        `;
                    } else if (url.includes('get_user_orders.php')) {
                        content = '<h2>Orders</h2>';
                        data.forEach(order => {
                            content += `
                                <div>
                                    <p><strong>Order ID:</strong> ${order.order_id}</p>
                                    <p><strong>Book:</strong> ${order.title}</p>
                                    <p><strong>Price:</strong> $${order.price}</p>
                                    <p><strong>Quantity:</strong> ${order.quantity}</p>
                                    <p><strong>Status:</strong> ${order.order_status}</p>
                                    <p><strong>Date:</strong> ${new Date(order.date).toLocaleString()}</p>
                                    <hr>
                                </div>
                            `;
                        });
                    }
                    contentSection.innerHTML = content;
                }
            })
            .catch(error => {
                contentSection.innerHTML = `<p>Error loading content: ${error.message}</p>`;
            });
    };

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
