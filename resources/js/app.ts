import { Application, Controller } from 'stimulus';
import { definitionsFromContext } from 'stimulus/webpack-helpers';
import platform from "./platform";

window.$ = window.jQuery = require('jquery');

require('popper.js');
require('bootstrap');
require('select2');

window.platform = platform();
window.application = Application.start();
window.Controller = Controller;

const context = require.context('./controllers', true, /\.ts$/);
window.application.load(definitionsFromContext(context));
