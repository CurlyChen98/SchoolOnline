// mygroup.js

const app = getApp();
const common = require("../../../utils/template.js")

Page({

  data: {
    modalHidden: true,
    modalTitle: '',
    modalKey: '',
    gid: '',
    ulevel: '',
    fors: [1, 2, 3, 4, 6, 7],
    a: "#03a9f4",
  },

  onLoad: function(options) {
    let gid = wx.getStorageSync('gid');
    let ulevel = wx.getStorageSync('ulevel');
    let uid = wx.getStorageSync('uid');
    ulevel = 2;
    this.setData({
      gid: gid,
      ulevel: ulevel,
    })
    console.log(gid + "-" + ulevel + "-" + uid);
  },

  onShow: function() {
    console.log("打开我的小组");
  },

  // 进入小组
  inGroup: function() {
    common.model(this, false, "请输入小组密匙");
  },

  // 自制模态框里面输入框失去焦点
  modalInput: function(e) {
    this.setData({
      modalKey: e.detail.value,
    })
  },

  // 自制模态框点击取消
  modalCancel: function(e) {
    common.model(this, true, "请输入小组密匙");
  },

  // 自制模态框点击确认
  modalConfirm: function() {
    common.showLoading("验证中", true)
    let that = this;
    let uid = wx.getStorageSync('uid');
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
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
          common.showToast(res.data.error, 'none', 1000, true)
        } else if (res.data.talk == "Ok") {
          wx.setStorageSync('gid', res.data.gid);
          common.model(that, true, "请输入小组密匙");
          common.redirectTo('../mygroup/mygroup');
        }
      }
    })
  },

})