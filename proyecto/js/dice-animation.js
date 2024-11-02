document.addEventListener('DOMContentLoaded', function() {
    const diceForm = document.getElementById('dice-form');
    const diceContainer = document.getElementById('dice-container');
    
    if (diceContainer) {
        diceContainer.querySelector('#dice1').classList.add('roll');
        diceContainer.querySelector('#dice2').classList.add('roll');

        setTimeout(() => {
            diceContainer.querySelector('#dice1').classList.remove('roll');
            diceContainer.querySelector('#dice2').classList.remove('roll');
        }, 1000);
    }
});
