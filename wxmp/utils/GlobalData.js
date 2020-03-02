const get = (key = null, defaultValue = null) => {
    let value = null;
    let globalData = getApp().globalData;
    if (key === null) {
        value = globalData;
    } else {
        let items = key.split('.');
        for (let i in items) {
            let name = items[i];
            if (globalData.hasOwnProperty(name)) {
                globalData = globalData[name]
            } else {
                break;
            }
        }
        value = globalData ? globalData : defaultValue;
    }

    return value;
};

module.exports = {
    get: get,
};

