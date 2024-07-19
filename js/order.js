class order {
    items = Array();
    #numberOfItems = 0;
    #cartButton;
    itemCount;
    cartModal;
    constructor() {
        let buttons = document.getElementsByClassName("add_to_cart");
        let addItemBound = this.addItem.bind(this);
        let loadCartBound = this.loadCart.bind(this);
        this.#cartButton = document.getElementById("cart-button");
        this.#cartButton.addEventListener("click", loadCartBound)
        this.itemCount = document.getElementById("item-count");
        this.cartModal = document.getElementById("cart-modal");
        for (let button of buttons) {
            button.addEventListener("click", addItemBound);
        }
    }
    addItem(e) {
        var i = e.target.getAttribute("itemid");
        if (i != null) {
            if (i in this.items) {
                this.items[i] += 1;
            } else {
                this.items[i] = 1;
            }
            console.log(this.itemCount);
            this.itemCount.innerHTML = parseInt(this.itemCount.innerHTML) + 1;
            this.#numberOfItems += 1;
        } else {
            console.error("could not find item ID");
        }
    }
    loadCart() {
        let items = this.items;
        // Convert the items object into URL-encoded form data
        let formData = new URLSearchParams();
        for (const [key, value] of Object.entries(items)) {
            formData.append(key, value);
        }

        let url = 'cart.php';
        let nextPage = "";
        if(document.getElementById("portal-container") != null){
            url = "../" + url;
            nextPage = "employeePayment.php";
        }
        //build the checkout page
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        })
            .then(response => response.text())
            .then(html => {
                document.getElementById('cart-content').innerHTML = html;
                this.cartModal.style.display = "block";
                document.getElementById("close-modal").addEventListener("click", function () {
                    document.getElementById("cart-modal").style.display = "none";
                })
                if(nextPage != ""){
                    console.log("dsadas");
                    document.getElementById("cart-form").setAttribute("action",nextPage);
                    Array.from(document.getElementsByTagName("img")).forEach( image=>{
                        image.setAttribute("src","../" + image.getAttribute("src"))
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    deleteItem(itemId) {
        let row = document.getElementById("cart-row-" + itemId);
        let itemCost = parseFloat(document.getElementById("cart-item-price-" + itemId).innerHTML);

        var quantity = this.items[itemId];
        //update order total
        var totalElement = document.getElementById("total-cost");
        var currentValue = parseFloat(totalElement.innerHTML.replace('$', ''));
        //format total value 
        var newValue = currentValue - itemCost;
        var formattedValue = '$' + newValue.toFixed(2);
        totalElement.innerHTML = formattedValue;
        //delete item from order and checkout
        delete this.items[itemId];
        this.#numberOfItems -= quantity;
        this.itemCount.innerHTML = this.#numberOfItems;

        row.remove();
    }
    checkoutOrder() {
        if (this.#numberOfItems == 0) {
            alert("We love how egar you are; but you have to get at least one item to checkout!");
        } else {
            document.getElementById("cart-form").submit();
        }
    }

}

o = new order();