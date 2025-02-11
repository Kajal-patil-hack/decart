
// Global object to buffer cart additions per product
let cartBuffer = {};
const cartItems = document.getElementById('cart-items');
/**
 * Called when the user clicks "Add to Cart".
 * Instead of sending a request immediately, it aggregates clicks (quantity)
 * for each product in the cartBuffer.
 *
 * @param {string} prodID - The product ID.
 * @param {string} prodName - The product name.
 * @param {number} price - The product price.
 */
function addToCart(prodID, prodName, price) {
    // Initialize an entry for this product if not already present
    if (!cartBuffer[prodID]) {
        cartBuffer[prodID] = { prodName, price, quantity: 0 };
    }
    
    // Increase the buffered quantity for this product
    cartBuffer[prodID].quantity++;
    
 cartItems.innerHTML = '';
 if (Object.keys(cartBuffer).length === 0) {
        cartItems.textContent = 'No items in cart';
    } else {
        let index = 1;
        // Iterate over each product in the buffer
        for (const prodID in cartBuffer) {
            if (cartBuffer.hasOwnProperty(prodID)) {
                const item = cartBuffer[prodID];
                // Create a div element to display product details
                let actPrice = item.price*item.quantity;
                const div = document.createElement('div');
                div.textContent = `${index}. ${item.prodName} - Quantity: ${item.quantity} - Price: $${actPrice}`;
                cartItems.appendChild(div);
                index++;
            }
        }
 }
alert("Item Added TO Cart");

}

/**
 * Called when the user clicks the "Buy Now" button.
 * This function sends the buffered cart data to the server.
 */

function UpdateProductHandler(prodID,current_quant){
fetch('update_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        // Send the aggregated item data to the server
        body: JSON.stringify({prodID,current_quant}),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
        alert('product table updated successfully');
        } else {
            alert('Failed to update product. Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error("Error in update_product.php:", error);
        alert('Failed to update product  due to a network error.');
    });

}

async function buyNow() {
    try {
        let userId = await getUserDataHandler();
        if (!userId) {
            console.error("User ID not found. Cannot process cart.");
            return;
        }
        
        console.log("User ID:", userId);

        for (const prodID in cartBuffer) {
            if (cartBuffer.hasOwnProperty(prodID)) {
                const item = cartBuffer[prodID];
                console.log(`User ID: ${userId}, Product ID: ${prodID}, Name: ${item.prodName}, Price: ${item.price}, Quantity: ${item.quantity}`);

                // Send cart data to the server
                await addToCartPhpHandler(userId, prodID, item.prodName, item.price, item.quantity);

                // Get the current product quantity from the server
                let someValue = await getProductCurrentQuantity(prodID);
                console.log("some value",someValue);
                if (someValue) {
                    console.log("Fetched Current Quantity:", someValue,item.quantity,prodID);
                    
                    let current_quantity = someValue - item.quantity;
                    console.log(`Updated Quantity for Product ${prodID}: ${current_quantity}`);

                    // Update the product quantity on the server
                    await UpdateProductHandler(prodID, current_quantity);
                }
            }
        }

        // Clear cart after all operations are complete
        cartBuffer = {};

        // Redirect to thank-you page
        window.location.href = 'thankyou.html';

    } catch (error) {
        console.error("Error processing cart:", error);
    }
}


/**
 * Fetches the user data from the server.
 * Expects fetchUserData.php to return a JSON object like:
 * {
 *   "success": true,
 *   "users": [ { "Id": 35, "session_id": "..." } ]
 * }
 *
 * @returns {Promise<number|null>} A Promise that resolves to the user ID or null if not found.
 */
function getUserDataHandler() {
    return fetch('fetchUserData.php')
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            // Debug: Log the complete data received
            console.log("Fetched data from fetchUserData.php:", data);
            
            // Check if the response was successful and that the users array has at least one entry
            if (data.success && data.users && data.users.length > 0) {
                return data.users[0].Id; // Adjust key if needed (e.g., "Id" vs. "id")
            } else {
                console.log("User data fetch unsuccessful:", data.error);
                return null;
            }
        })
        .catch(error => {
            console.error("Error fetching user data:", error);
            return null;
        });
}

/**
 * Sends a request to add an item (or update quantity) in the cart.
 *
 * @param {number} userId - The ID of the logged-in user.
 * @param {string} prodID - The product ID.
 * @param {string} prodName - The product name.
 * @param {number} price - The product price.
 * @param {number} quantity - The aggregated quantity of the product.
 */
function addToCartPhpHandler(userId, prodID, prodName, price, quantity) {
    let showAlert=0;
fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        // Send the aggregated item data to the server
        body: JSON.stringify({ userId, prodID, prodName, price, quantity }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
	showAlert = showAlert + 1;
        } else {
            alert('Failed to add item to cart. Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error("Error in add_to_cart.php:", error);
        alert('Failed to add item to cart due to a network error.');
    });
console.log(showAlert);
if(!showAlert){

 alert('Item added to cart table!');

}

}


/**
 * Fetches the cart items from the server and displays them.
 */
function showCart() {

  document.getElementById('cart').style.display = 'block';
    // Show the cart modal
    

}

/**
 * Closes the cart modal.
 */
function closeCart() {
 document.getElementById('cart').style.display = 'none';

}


function getProductCurrentQuantity(prodID) {
    return fetch('current_product_quant.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ prodID }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then(data => {
        console.log("Fetched product quantity:", data);

        if (data.success && data.items.length > 0) {
            return data.items[0].current_quant; // Correct key name
        } else {
            console.log("Failed to fetch quantity:", data.message);
            return null;
        }
    })
    .catch(error => {
        console.error("Error fetching product quantity:", error);
        return null;
    });
}
