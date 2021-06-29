/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// require jQuery normally
import $ from 'jquery';
// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
// start the Stimulus application
import './bootstrap';
import { Tooltip, Toast, Popover } from 'bootstrap';
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything

// or you can include specific pieces
import 'bootstrap/js/dist/tooltip';
import 'bootstrap/js/dist/popover';
// require the JavaScript
import 'bootstrap-star-rating';
// require 2 CSS files needed
import 'bootstrap-star-rating/css/star-rating.css';
import 'bootstrap-star-rating/themes/krajee-svg/theme.css';


import { createPopper } from '@popperjs/core';
const popcorn = document.querySelector('#popcorn');
const tooltip = document.querySelector('#tooltip');
createPopper(popcorn, tooltip, {
    placement: 'right',
});
createPopper(popcorn, tooltip);