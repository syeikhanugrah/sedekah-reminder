import 'bootstrap';

global.$ = global.jQuery = require('jquery');

require('jquery-ui');
require('jquery-ui/ui/widgets/datepicker');

$.datepicker.setDefaults({
    dateFormat: 'dd/mm/yy',
    changeMonth: true,
    changeYear: true
});
