function flash(message = "", color = "info") {
    let flash = document.getElementById("flash");
    //create a div (or whatever wrapper we want)
    let outerDiv = document.createElement("div");
    outerDiv.className = "row justify-content-center";
    let innerDiv = document.createElement("div");

    //apply the CSS (these are bootstrap classes which we'll learn later)
    innerDiv.className = `alert alert-${color}`;
    //set the content
    innerDiv.innerText = message;

    outerDiv.appendChild(innerDiv);
    //add the element to the DOM (if we don't it merely exists in memory)
    flash.appendChild(outerDiv);
}

if (document.readyState == 'loading')
    document.addEventListener('DOMContentLoaded', ready)
else ready()
    
// cart management functions
function ready(){
    //for remove from cart in cart
    var remove = document.getElementsByClassName('btn-danger');
    for (var i = 0; i< remove.length; i++){
        var button = remove[i];
        button.addEventListener('click', remove_from_cart)
    }
    //for change quantity slider
    var quantityInputs = document.getElementsByClassName('cart-item-id') //in the html any div tag displaying product id will have this class
    for (var i = 0; i< quantityInputs.length; i++){
        var input = quantityInputs[i]
        input.addEventListener('change',change_Quantity)
    }
    //for add to cart button in shop.php or item_page.php
    var add_to_cart_buttons = document.getElementsByClassName('btn btn-primary')
    for (var i = 0; i< add_to_cart_buttons.length; i++){
        var button = add_to_cart_buttons[i]
        button.addEventListener('click',add_to_cart)
    }

    var empty_cart = doucument.getElementsByClassName('btn-purchase')[0].addEventListener('click', purchase)
}

function purchase(){
    flash("Purchase Successful. Thank You!")
    var cartItems = document.getElementsByClassName('cart-items')
    while (cartItems.hasChildNodes()){
        cartItems.removeChild(cartItems.firstChild())
    }
    update_cart_total()
}

function add_to_cart(event){
    var button = event.target
    var shopItem = button.parentElement.parentElement
    var title = shopItem.getElementsByClassName('shop-item-title')[0].innerText
    var price = shopItem.getElementsByClassName('shop-item-price')[0].innerText
    var image = shopItem.getElementsByClassName('shop-item-image')[0].src
    addItemToCart(title,price,image)
    update_cart_total()
}

function addItemToCart(title,price,image){
    var cart_row = document.createElement('div')
    cart_row.classList.add('cart-row')
    var cartItems = document.getElementsByClassName('cart-items')[0]
    var cart_item_names = cartItems.getElementsByClassName('cart-item-title')
    for (var o = 0; o < cart_item_names.length; o++){
        if (cart_item_names[i].innerText == title){
            flash("This item is already in the cart")
            
        }
    }
    // add all of the cart styling here
    var cart_row_contents = `
        <div class="cart-item cart-column">
            <img class="cart-item-image" src=${image} width="100" height="100">
            <span class="cart-item-title">${title}</span>
        </div>
        <span class="cart-price cart-column">${price}</span>
        <div class="cart-quantity cart-column">
            <input class="cart-quantity-input" type="number" value="1">
            <button class="btn btn-danger" type="button">REMOVE</button>
        </div>
    `
    cart_row.innerHTML = cart_row_contents
    cartItems.append(cart_row)
    cart_row.getElementsByClassName('btn-danger')[0].addEventListener('click',remove_from_cart)
    cart_row.getElementsByClassName('cart-quantity-input')[0].addEventListener('change',change_Quantity)
}

function change_Quantity(event){ 
    var input = event.target
    if (isNaN(input.value) || input.value < 0){
        input.value = 0
    }
    update_cart_total()
}

function remove_from_cart(event){
    var button_clicked = event.target
    button_clicked.parentElement.parentElement.remove()
    update_cart_total()
}

// the idea is to get load the data from the database onto the page, then
// use these js functions to take the data from the page and maniupulate it
function update_cart_total(){
    var cartItemContainer = document.getElementsByClassName('cart-items')[0]
    var cartRows = cartItemContainer.getElementsByClassName('cart-row')
    var total = 0
    for (var i = 0; i< cartRows.length; i++){
        var row = cartRows[i]
        var priceEle = row.getElementsByClassName('cart-price')[0]
        var quantityEle = row.getElementsByClassName('cart-quantity-input')[0]
        var price = parseFloat(priceEle.innerText.replace('$',' '))
        var quantiy = quantityEle.value
        total = total + (price*quantiy)
    }
    total = total / 100
    document.getElementsByClassName('cart-total-price')[0].innerText = '$' + total
}

// use this function to insert the cart into the database
function insert_cart_db(){

}