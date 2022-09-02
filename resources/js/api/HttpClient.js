import axios from 'axios';

class HttpClient {
    constructor() {
        const config = {
            baseURL: process.env.VUE_APP_BASE_URL || '',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        };

        this._client = axios.create(config);
    }

    bindToken(token) {
        this._client.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    removeToken() {
        delete this._client.defaults.headers.common['Authorization'];
    }

    get(url, config = {}) {
        return this._client.get(url, config);
    }

    post(url, data = {}, config = {}) {
        return this._client.post(url, data, config);
    }

    put(url, data = {}, config = {}) {
        return this._client.put(url, data, config);
    }

    delete(url, config = {}) {
        return this._client.delete(url, config);
    }
}

export default HttpClient;
