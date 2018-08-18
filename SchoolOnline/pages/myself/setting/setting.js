// setting.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    modalHidden: true,
    modalTitle: '',
    modalKey: '',
    ulevel: 0,
    uid: 0,
    cid: 0,
  },

  onLoad: function() {
    let ulevel = wx.getStorageSync('ulevel');
    let uid = wx.getStorageSync('uid');
    let cid = wx.getStorageSync('cid');
    this.setData({
      ulevel: ulevel,
      uid: uid,
      cid: cid,
    })
  },

  // 点击输入密匙方法
  levelCheck: function() {
    if (this.data.ulevel == "5" || this.data.ulevel == "10") {
      common.showToast('已是高等级', 'none', 1000, true);
      return;
    }
    common.model(this, false, "请输入老师密匙")
  },

  // 模拟弹窗提交触发方法
  confirm: function() {
    common.model(this, true, "")
    let modalkeyInput = '';

    var query = wx.createSelectorQuery()
    query.select('#modalInput').fields({
      properties: ['value'],
    }, function(res) {
      modalkeyInput = res.value;
      console.log(modalkeyInput)
    });
    query.exec();
    this.setData({
      modalKey: '',
    })

    console.log(modalkeyInput)
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "CheckTeacher",
        uid: this.data.uid,
        cid: this.data.cid,
        key: modalkeyInput,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data)
        if (res.data.talk == "Ok") {
          wx.setStorageSync('ulevel', res.data.level);
          common.showToast('等级提升成功', 'success', 1000, true);
        } else {
          common.showToast(res.data.error, 'none', 1000, true);
        }
      }
    })

  },

})