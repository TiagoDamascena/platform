import Turbolinks from 'turbolinks';
import axios from 'axios'

/**
 * Creating an instance Axios
 */
const instance = axios.create();


const startProgressBar = () => {
    if (!Turbolinks.supported) {
        return;
    }
    Turbolinks.controller.adapter.progressBar.setValue(0);
    Turbolinks.controller.adapter.progressBar.show();
};

const stopProgressBar = () => {
    if (!Turbolinks.supported) {
        return;
    }
    Turbolinks.controller.adapter.progressBar.hide();
    Turbolinks.controller.adapter.progressBar.setValue(100);
};


// Add a request interceptor
instance.interceptors.request.use((config) => {
    // Do something before request is sent

    startProgressBar();
    return config;
}, (error) => {

    stopProgressBar();
    // Do something with request error
    return Promise.reject(error);
});

// Add a response interceptor
instance.interceptors.response.use((response) => {
    // Do something with response data
    stopProgressBar();
    return response;
}, (error) => {
    // Do something with response error
    stopProgressBar();
    return Promise.reject(error);
});


/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
instance.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector<HTMLMetaElement>(
    'meta[name="csrf-token"]'
).content;


export default instance
