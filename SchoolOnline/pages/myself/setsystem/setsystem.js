// setsystem.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    classList: '',
  },

  onLoad: function(options) {
    let that = this;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "ShowAllClass",
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data);
        that.setData({
          classList: res.data.class,
        });
      }
    })
  },

  onShow: function() {
    common.setNav("系统管理")
  },

  deleteClassCheck: function(e) {
    let cid = e.currentTarget.dataset.cid;
    let that = this;
    wx.showModal({
      title: '警告',
      content: '确认是否删除！',
      success: function(res) {
        if (res.confirm) {
          that.deleteClass(cid);
        }
      }
    })
  },

  deleteClass: function(cid) {
    console.log(cid)
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "DeleteClass",
        cid: cid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data);
      }
    })
  },

})