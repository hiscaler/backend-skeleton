const apiHost = getApp().globalData.config.apiHost,
    Identity = require('./Identity');

const toRoute = (router, params = {}) => {
    let url = apiHost + router;
    if (Object.keys(params).length) {
        let query = [];
        for (let key in params) {
            query.push(`${key}=${params[key]}`);
        }
        url += '?' + query.join("&");
    }
    const token = Identity.get('access_token');
    if (token) {
        if (url.indexOf('?') === -1) {
            url += '?';
        } else {
            url += '&';
        }
        url += `access_token=${token}`;
    }

    return url;
};


module.exports = {
    toRoute: toRoute,
};

