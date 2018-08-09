App({

  // 自定义全局变量
  globalData: {
    // 后台地址
    // backAddress: 'http://localhost/SchoolOnline/',
    backAddress: 'https://www.bigcurly.club/SchoolOnline/',
    backPage: 'Php/use.php',
    backDownloadJob: 'Html/dwLoadFiles.html',
    backUploadJob: 'Html/upLoadFiles.html',
  },

  onLaunch: function() {
    console.log("打开小程序");
  },

  onShow: function(options) {
    // 截图触发事件
    wx.onUserCaptureScreen(function(res) {
      wx.showModal({
        title: '截图了？',
        content: '欢迎反馈至z876786569@163.com',
        showCancel: false,
        confirmText: '明白',
        success: function(res) {}
      })
    })
  },

  onHide: function() {

  },

  onError: function(msg) {

  },
})