// setting.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    modalHidden: true,
    modalTitle: '',
    modalkey: '',
    key: '',
    ulevel: 0,
    uid: 0,
  },

  onLoad: function() {
    let ulevel = wx.getStorageSync('ulevel');
    let uid = wx.getStorageSync('uid');
    this.setData({
      ulevel: ulevel,
      uid: uid,
    })
  },

  onShow: function() {
    console.log("打开设置")
  },

  // 点击输入老师密匙方法
  levelCheck: function() {
    if (this.data.ulevel == "5") {
      wx.showToast({
        title: '已是老师等级',
        icon: 'none',
        duration: 1000,
        mask: true,
      })
      return;
    }
    common.model(this, false, "请输入班级老师密匙")
    console.log("长按身份")
  },

  // 模拟弹窗输入框失去焦点方法
  modalInput: function(e) {
    this.setData({
      modalkey: e.detail.value,
      key: '',
    })
  },

  // 模拟弹窗提交触发方法
  confirm: function() {
    common.model(this, true, "请输入班级老师密匙")
    console.log("提交老师密匙：" + this.data.modalkey)
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "CheckTeacher",
        uid: this.data.uid,
        teacherkey: this.data.modalkey,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data)
        if (res.data.talk = "Ok") {
          wx.setStorageSync('ulevel', '5')
          wx.showToast({
            title: '等级提升成功',
            icon: 'success',
            duration: 2000,
            mask: true,
          })
        }
      }
    })
  },

})