// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import './bootstrap';
import { Tooltip, Toast, Popover } from 'bootstrap';
import 'popper.js';

const $ = require('jquery');

$(function () {
  const sidebarWidth = $("#sidebar").outerWidth();
  $("#content").css("padding-left", sidebarWidth);
});