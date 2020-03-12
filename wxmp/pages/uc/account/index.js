const Url = require('../../../utils/Url.js'),
    Identity = require('../../../utils/Identity.js');

Page({
    data: {
        form: {},
        files: [
            {
                url: '',
                error: false,
            },
        ]
    },
    onLoad: function(options) {
        if (Identity.isGuest()) {
            Identity.toLoginPage();
        } else {
            wx.showLoading({ title: '数据加载中...' });
            wx.request({
                url: Url.toRoute('/account/view'),
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        this.setData({
                            form: {
                                username: resp.data.username,
                                nickname: resp.data.nickname,
                                real_name: resp.data.real_name,
                                mobile_phone: resp.data.mobile_phone,
                                remark: resp.data.remark,
                            },
                            files: [
                                {
                                    url: resp.data.avatar,
                                    error: false,
                                }
                            ]
                        });
                        wx.createSelectorQuery()
                            .select('#remark')
                            .context(function(res) {
                                res.context.setContents({
                                    html: resp.data.remark
                                })
                            }).exec()
                    } else {
                        wx.showModal({
                            title: '提示',
                            content: resp.error.message,
                            showCancel: false,
                        });
                    }
                },
                complete: function(res) {
                    wx.hideLoading({});
                }
            });
            this.setData({
                selectFile: this.selectFile.bind(this),
                uploadFile: this.uploadFile.bind(this)
            });
        }
    },
    chooseImage: function(e) {
        let that = this;
        wx.chooseImage({
            sizeType: ['original', 'compressed'],
            sourceType: ['album', 'camera'],
            success: function(res) {
                that.setData({
                    files: that.data.files.concat(res.tempFilePaths)
                });
            }
        })
    },
    previewImage: function(e) {
        wx.previewImage({
            current: e.currentTarget.id,
            urls: this.data.files
        });
    },
    selectFile(files) {
        // 返回 false 可以阻止某次文件上传
        console.log('files', files);
    },
    uploadFile(files) {
        console.log('upload files', files);
        // 文件上传的函数，返回一个promise
        return new Promise((resolve, reject) => {
            if (files.hasOwnProperty('contents') && files.contents.length) {
                const contents = files.contents[0];
                const file = files.tempFilePaths[0];
                const fileExtension = file.split('.').pop().toLowerCase();
                let mimeType;
                switch (fileExtension) {
                    case 'png':
                        mimeType = 'image/png';
                        break;

                    case 'gif':
                        mimeType = 'image/gif';
                        break;

                    default:
                        mimeType = 'image/jpg';
                        break;
                }
                const avatar = `data:${mimeType};base64,` + wx.arrayBufferToBase64(contents);
                this.setData({
                    form: {
                        ...this.data.form,
                        ...{ 'avatar': avatar }
                    }
                });
                let object = {
                    urls: [avatar],
                };
                resolve(object);
            } else {
                reject('请上传文件。');
            }
        })
    },
    uploadError(e) {
        console.log('upload error', e.detail);
    },
    uploadSuccess(e) {
        console.log('upload success', e.detail);
    },
    // 数据双向绑定
    bindField: function(e) {
        let dataset = e.currentTarget.dataset,
            name = dataset.name,
            value = e.detail.value,
            form = this.data.form;
        form[name] = value;
        this.setData({
            form,
        });
    },
    bindRemark: function(e) {
        console.info(e);
        this.setData({
            form: {
                ...this.data.form,
                ...{ remark: e.detail.html }
            }
        });
    },
    // 修改个人资料
    updateHandle: function(e) {
        let isValid = true,
            message = '';
        const form = this.data.form;
        if (!form.nickname) {
            isValid = false;
            message = '请输入昵称';

        }
        if (isValid && !form.real_name) {
            isValid = false;
            message = '请输入姓名';
        }
        if (isValid && !form.mobile_phone) {
            isValid = false;
            message = '请输入手机号码';
        }
        if (!isValid) {
            wx.showToast({
                title: message,
                icon: "none",
            });
        } else {
            wx.showLoading({
                title: '处理中...'
            });
            delete form['username'];
            wx.request({
                url: Url.toRoute('/account/update'),
                data: form,
                method: "PUT",
                success: res => {
                    const resp = res.data;
                    if (resp.success) {
                        let identity = wx.getStorageSync('identity');
                        if (identity) {
                            identity = { ...identity, ...form };
                            wx.setStorageSync('identity', identity);
                        }
                        wx.showToast({
                            title: '修改成功',
                        });
                    } else {
                        let message = '';
                        const error = resp.error;
                        if (Array.isArray(error)) {
                            for (let i in error) {
                                message = error[i].message;
                                break;
                            }
                        } else {
                            message = error.message;
                        }
                        wx.showModal({
                            title: '提示',
                            content: message,
                            showCancel: false,
                        });
                    }
                },
                complete: function(res) {
                    wx.hideLoading({});
                }
            });
        }
    }
});
