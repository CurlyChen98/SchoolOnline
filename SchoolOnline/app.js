App({

  // 自定义全局变量
  globalData: {
    // 后台地址
    backAddress: 'http://localhost/SchoolOnlineBack/',
    // backAddress: 'https://www.bigcurly.club/SchoolOnline/',    
  },

  onLaunch: function() {

  },

  onShow: function(options) {
    // 截图触发事件
    wx.onUserCaptureScreen(function(res) {
      wx.showModal({
        title: '截图了？',
        content: '欢迎反馈至someEmail@email.com',
        showCancel: false,
        confirmText: '明白',
        success: function(res) {
          if (res.confirm) {
            console.log('欢迎反馈')
          }
        }
      })
    })
  },

  onHide: function() {

  },

  onError: function(msg) {

  },
})
