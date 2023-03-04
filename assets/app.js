// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import './bootstrap';
import { Tooltip, Toast, Popover } from 'bootstrap';
import 'popper.js';
import 'fullcalendar';

const $ = require('jquery');

$(function () {

  if ($(document).width() >= 768) {
    const sidebarWidth = $("#sidebar").outerWidth();
    $("#content").css("padding-left", sidebarWidth);
  }

  if ($(document).width() < 768) {
    const blockTitle = $(".block-title");
    blockTitle.addClass("block-title-mobile");
  }

  // Set field Arrhes to 30% of price per default
  const priceField = $("#reservation_price");
  const arrhesField = $("#reservation_arrhes");
  const leftToPayField = $("#reservation_leftToPay");
  let arrhes = 0;
  let leftToPay = 0;
  priceField.on('change', () => {
    const price = priceField.val();
    arrhes = (price * 0.3);
    arrhesField.val(arrhes);
    leftToPay = (price - arrhes);
    leftToPayField.val(leftToPay);
  });

  // Calculate the solde field value when arrhes value change.
  arrhesField.on('change', () => {
    const price = priceField.val();
    arrhes = arrhesField.val();
    leftToPay = (price - arrhes);
    leftToPayField.val(leftToPay);
  });

  // Ajax request for validation payments in reservation
  const inputsPayment = document.querySelectorAll('.validate-payment');

  inputsPayment.forEach(payment => {
    payment.addEventListener('change', () => {
      let value = payment.checked;
      console.log(value);
      console.log(payment.name);
      let url = payment.getAttribute("data-path")
      console.log(url);
      
      $.ajax({ 
        type: 'POST', 
        url: url,
        data: {'paymentName' : payment.name, 'paymentValue' : value}, 
        dataType: 'json',
        success: function (data) { 
            console.log(data);
        }
      });
    });
  });

});
