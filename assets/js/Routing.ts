import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/js/router';
const routes = require('./fos_js_routes.json');

Routing.setRoutingData(routes);

export default Routing;