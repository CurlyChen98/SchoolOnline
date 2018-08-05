// login.js

const app = getApp();

Page({

  data: {

  },

  onShow: function() {
    console.log("进入注册")
  },

  formSubmit: function(e) {
    let classkey = e.detail.value.classkey;
    let studentkey = e.detail.value.studentkey;
    let back = app.globalData.backAddress;
    let that = this;
    wx.login({
      success: function(res) {
        let code = res.code;
        wx.request({
          url: back,
          data: {
            do: "InsertUse",
            code: code,
            classkey: classkey,
            studentkey: studentkey,
          },
          method: 'POST',
          header: {
            'content-type': 'application/x-www-form-urlencoded'
          },
          success: function(res) {
            console.log(res.data)
          }
        })
      }
    });
  },

})