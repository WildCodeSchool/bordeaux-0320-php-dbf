/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import { Application } from "@hotwired/stimulus"
import { definitionsFromContext } from "@hotwired/stimulus-webpack-helpers"

window.Stimulus = Application.start()
const context = require.context("./../controllers", true, /\.js$/)
Stimulus.load(definitionsFromContext(context))

// any CSS you require will output into a single css file (app.css in this case)
require('../scss/app.scss');


// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');


require('./materialize.js');
require('./base.js');
require('./CallsCounter');
require('./switchHasCome');
require('./CallFormValidator');
require('./AddCall');
require('./admin.js');
require('./clientFormAdmin');
require('./recipientForm');
require('./recipientTransfer');
require('./searchClientByPhone');
require('./reattributePhone');
require('./deletor');
require('./transferCall');
require('./searchPage');
require('./editor');
require('./newUser');
require('./userAdmin');
require('./deleteCall');
require('./Collapsor');
require('./resetVehicule');
require('./userHighlighting');
require('./copyer');
require('./headDeletor');
require('./ContactFormServicesLister');
require('./DatabaseInitializer');
//require('./CheckSession');

