// mygroup.js

const app = getApp();
const common = require("../../../utils/template.js")

Page({

  data: {
    modalHidden: '',
    modalTitle: '',
    modalKey: '',
    level1: 2,
    level2: 1,
  },

  onLoad: function(options) {
    let gid = wx.getStorageSync('gid');
    let that = this;
    console.log(gid)
    if (gid == "0") {
      common.model(that, true, "请输入小组密匙");
    }
  },

  onShow: function() {
    console.log("打开我的小组");
  },

  modalInput: function(e) {
    this.setData({
      modalKey: e.detail.value,
    })
  },

  modalConfirm: function() {
    common.showLoading("验证中", true)
    let that = this;
    let uid = wx.getStorageSync('uid');
    wx.request({
      url: app.globalData.backAddress,
      data: {
        do: "CheckGroup",
        groupkey: that.data.modalKey,
        uid: uid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        common.hideLoading();
        console.log(res.data)
        if (res.data.talk == "NotOk") {
          wx.showModal({
            title: '',
            content: res.data.error,
            showCancel: '',
          })
        } else if (res.data.talk == "Ok") {
          common.model(that, true, "请输入小组密匙");
        }
      }
    })
  },

})