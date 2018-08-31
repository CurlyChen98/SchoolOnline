// createNote.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    date: '',
    dateYear: '',
    coursearr: '',
    index: '',
  },

  onLoad: function(options) {
    let that = this;

    let date = new Date();
    let year = date.getFullYear();
    let mon = date.getMonth() + 1;
    let day = date.getDate();
    if (mon < 10) mon = "0" + mon;
    if (day < 10) day = "0" + day;
    let dateYear = year + "-" + mon + "-" + day;
    date = mon + "/" + day;

    let cid = wx.getStorageSync('cid')
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "FindCourse",
        cid: cid
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        let coursearr = res.data.course;
        that.setData({
          date: date,
          dateYear: dateYear,
          coursearr: coursearr,
        })
      }
    })
  },

  // 滚动选择器发生改变
  courseChange: function(e) {
    let index = e.detail.value;
    this.setData({
      index: index
    })
  },

  // 点击表单提交
  formsub: common.throttle(function(e) {
    let data = e.detail.value;
    let course = this.data.coursearr[data.course];
    let uid = wx.getStorageSync('uid');
    let dateYear = this.data.dateYear;
    if (data.course == "" || data.detail == "" || data.title == "") {
      wx.showToast({
        title: '填写不完全',
        icon: 'none',
        duration: 1000,
      })
      return;
    }
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "CreateTalk",
        data: JSON.stringify(data),
        course: JSON.stringify(course),
        uid: uid,
        dateYear: dateYear,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        if (res.data.talk == "Ok") {
          wx.showToast({
            title: '发布成功',
            icon: 'success',
            duration: 1000,
            success: function () {
              setTimeout(function () {
                wx.reLaunch({
                  url: '../topic'
                })
              }, 1000)
            }
          })
        } else {
          wx.showToast({
            title: '发布失败,请咨询开发者',
            icon: 'none',
            duration: 1000,
            success: function () {
              setTimeout(function () {
                wx.reLaunch({
                  url: '../topic'
                })
              }, 1000)
            }
          })
        }
      }
    })
  }, 1000),

})