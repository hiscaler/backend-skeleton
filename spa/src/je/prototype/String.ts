/**
 * 扩展字符串功能
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
declare interface String {
    /**
     * 判断是否为空
     * @param {string} str
     * @returns {boolean}
     */
    isEmpty(str: string): boolean;

    /**
     * 判断是否以某字符开头
     * @param {string} str
     * @returns {boolean}
     */
    startsWith(str: string): boolean;

    /**
     * 判断是否以某字符结尾
     * @param {string} str
     * @returns {boolean}
     */
    endWith(str: string): boolean;
}

String.prototype.isEmpty = function (str: string) {
    str = str.trim();

    return str.length ? true : false;
}

String.prototype.startsWith = function (str: string) {
    return this.indexOf(str) === 0 ? true : false;
}

String.prototype.endWith = function (str: string) {
    let i = this.indexOf(str);
    return i && (i + str.length) === this.length ? true : false;
}

