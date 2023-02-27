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
  priceField.on('change', () => {
    const price = priceField.val();
    const arrhes = (price * 0.3);
    arrhesField.val(arrhes);
    const leftToPay = (price - arrhes);
    leftToPayField.val(leftToPay);
  });
});
