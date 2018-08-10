// login.js

const app = getApp();

Page({

  data: {

  },

  onShow: function() {
    console.log("进入注册")
  },

  formSubmit: function(e) {
    wx.showLoading({
      title: '加载中',
      mask: true,
    });
    let classkey = e.detail.value.classkey;
    let studentkey = e.detail.value.studentkey;
    let that = this;
    wx.login({
      success: function(res) {
        let code = res.code;
        wx.request({
          url: app.globalData.backAddress + app.globalData.backPage,
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
            wx.hideLoading();
            if (res.data.talk == "Ok") {
              wx.showToast({
                title: '添加成功',
                icon: 'success',
                duration: 1000,
                success: function(res) {
                  setTimeout(function() {
                    wx.reLaunch({
                      url: '../index/index'
                    })
                  }, 1000)
                }
              })
            } else if (res.data.talk == "NotOk") {
              wx.showModal({
                title: '',
                content: res.data.error,
                showCancel: '',
              })
            }
          }
        })
      }
    });

  },

})