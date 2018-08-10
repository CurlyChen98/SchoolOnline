// index.js

const app = getApp();

Page({

  data: {
    arrcourse: '',
  },

  onLoad: function(options) {
    wx.showLoading({
      title: '加载中',
      mask: true,
    });
    let that = this;
    wx.login({
      success: function(res) {
        let code = res.code;
        wx.request({
          url: app.globalData.backAddress + app.globalData.backPage,
          data: {
            do: "SelectUse",
            code: code
          },
          method: 'POST',
          header: {
            'content-type': 'application/x-www-form-urlencoded'
          },
          success: function(res) {
            wx.hideLoading()
            if (res.data.talk == "NotOk") {
              wx.reLaunch({
                url: '../login/login'
              })
            } else if (res.data.talk == "Ok") {
              that.setData({
                classkey: res.data.class.name,
                studentkey: res.data.use.name,
                arrcourse: res.data.course,
              })
              wx.clearStorageSync();
              wx.setStorageSync("cid", res.data.class.cid);
              wx.setStorageSync("cname", res.data.class.name);
              wx.setStorageSync("uid", res.data.use.uid);
              wx.setStorageSync("uname", res.data.use.name);
              wx.setStorageSync("ulevel", res.data.use.level);
              wx.setStorageSync("gid", res.data.use.gid);
            }
          }
        })
      }
    });
  },

  onShow: function() {
    console.log("打开课程")
  },

  // 点击触发课程事件
  jumpnext: function(e) {
    let couid = e.currentTarget.dataset.couid;
    wx.setStorageSync('couid', couid);
    wx.navigateTo({
      url: 'classroom/classroom',
    });
  },

})