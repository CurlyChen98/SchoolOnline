// myclass.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    studentList: '',
  },

  onLoad: function(options) {
    let cid = wx.getStorageSync("cid");
    let that = this;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "FindMyClass",
        cid: cid
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        that.setData({
          studentList: res.data.studentList,
        });
      }
    })
  },

  deleteStudent: function(e) {
    common.showLoading("删除中", true);
    let uid = e.currentTarget.dataset.uid;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "DeleteStudent",
        uid: uid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data)
        common.hideLoading();
        if (res.data.talk == "Ok") {
          common.redirectTo("../myclass/myclass")
        } else if (res.data.talk == "NotOk") {
          common.showToast("res.data.error", "none", 1000, true);
        }
      }
    })
  },

})