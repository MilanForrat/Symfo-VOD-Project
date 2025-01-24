btnEmptyBasket = document.getElementById('emptyBasket');

btnEmptyBasket.addEventListener('click', confirmEmptyBasket);
function confirmEmptyBasket(){
    if(confirm("Vider l'ensemble du panier ?")==true){
        window.location.href = "http://127.0.0.1:8000/panier/deleteAll";
    }
    else{
        window.location.href = "http://127.0.0.1:8000/panier";
    }
};
