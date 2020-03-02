const GlobalData = require('./GlobalData.js');

const get = (key, defaultValue = null) => {
    let value = null;
    const identity = wx.getStorageSync('identity');
    if (identity && identity.hasOwnProperty(key)) {
        value = identity[key];
        value = value ? value : defaultValue;
    }

    return value;
};

const isGuest = () => {
    let isGuest = true;
    if (get('access_token')) {
        isGuest = false;
    }

    return isGuest;
};

const toLoginPage = () => {
    wx.redirectTo({
        url: GlobalData.get('config.loginUrl', '/pages/uc/login/index')
    });
};

module.exports = {
    get: get,
    isGuest: isGuest,
    toLoginPage: toLoginPage,
};

