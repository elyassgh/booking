window.scroll({
    behavior: 'smooth'
});

var today = new Date().toISOString().split('T')[0];
var tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1)
tomorrow = tomorrow.toISOString().split('T')[0];

document.getElementById("form_checkin").setAttribute('min', today);
document.getElementById("form_checkout").setAttribute('min', tomorrow);


document.getElementById('form_checkin').addEventListener('change', function () {
    var minCheckOut = new Date(this.value);
    minCheckOut.setDate(minCheckOut.getDate() + 1);
    minCheckOut = minCheckOut.toISOString().split('T')[0];
    document.getElementById('form_checkout').setAttribute('min', minCheckOut);
});

document.getElementById('form_checkout').addEventListener('change', function () {
    var maxCheckout = new Date(this.value);
    maxCheckout.setDate(maxCheckout.getDate() - 1);
    maxCheckout = maxCheckout.toISOString().split('T')[0]
    document.getElementById('form_checkin').setAttribute('max', maxCheckout);
});

// Slick Carousel init
$(document).ready(function(){
    $('.customer-logos').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2500,
        arrows: false,
        dots: false,
        pauseOnHover: false,
        responsive: [{
            breakpoint: 768,
            settings: {
                slidesToShow: 3
            }
        }, {
            breakpoint: 520,
            settings: {
                slidesToShow: 2
            }
        }]
    });
});