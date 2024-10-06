document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll('.button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            alert('Botón clicado!');
        });
    });

    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close');

    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.parentElement.parentElement.style.display = 'none';
        });
    });

    window.onclick = function(event) {
        modals.forEach(modal => {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
 
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close');

    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            btn.parentElement.parentElement.style.display = 'none';
        });
    });

    window.onclick = function(event) {
        modals.forEach(modal => {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    };
});
// scripts.js

document.addEventListener('DOMContentLoaded', function(){
    // Obtener todos los botones que abren modales
    var modalBtns = document.querySelectorAll('.btn[data-modal]');
    
    modalBtns.forEach(function(btn){
        btn.onclick = function(){
            var modal = btn.getAttribute('data-modal');
            document.getElementById(modal).style.display = 'block';
        };
    });

    // Obtener todos los elementos que cierran modales
    var closeBtns = document.querySelectorAll('.close[data-modal]');
    
    closeBtns.forEach(function(btn){
        btn.onclick = function(){
            var modal = btn.getAttribute('data-modal');
            document.getElementById(modal).style.display = 'none';
        };
    });

    // Cerrar el modal al hacer clic fuera del contenido
    window.onclick = function(event){
        if(event.target.classList.contains('modal')){
            event.target.style.display = 'none';
        }
    };
});
