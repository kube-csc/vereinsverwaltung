import _ from 'lodash';
import axios from 'axios';
import 'boxicons/dist/boxicons.js';

window._ = _;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Alpine.js wird mit Livewire 3 (@livewireScripts) geliefert und darf nicht
// manuell eingebunden werden.