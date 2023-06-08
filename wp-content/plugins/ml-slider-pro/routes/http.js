import axios from 'axios'

const Axios = axios.create({
    baseURL: window.metasliderpro_api.supports_rest ? window.metasliderpro_api.root : false,
    headers: {
        'X-WP-Nonce': window.metasliderpro_api ? window.metasliderpro_api.nonce : false,
        'X-Requested-With': 'XMLHttpRequest'
    }
})

Axios.interceptors.request.use((config) => {

    // If the baseURL above is false, it means that REST is not supported
    // So we can override the route to use admin-ajax.php
    if (!config.baseURL) {
        config.url = window.metasliderpro_api.ajaxurl
    }

    return config
})

export default Axios
