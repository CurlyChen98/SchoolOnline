// classroom.js

const app = getApp();

Page({

  data: {
    data: '',
  },

  onLoad: function(options) {
    let couid = wx.getStorageSync('couid');
    let that = this;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "ClassRoom",
        couid: couid
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        let data = res.data.course[0];
        console.log(data)
        that.setData({
          data: data,
        })
      }
    })
  },

  onShow: function() {
    console.log("打开课程")
  },

  homeworkDownload: function() {
    let data = this.data.data;
    let cid = data.cid;
    let couid = data.couid;
    let src = app.globalData.backAddress + app.globalData.backDownloadJob + "?cid=" + cid + "&couid=" + couid;
    wx.showModal({
      title: '提示',
      content: '微信不支持该链接，确认复制链接到浏览器打开下载',
      showCancel: false,
      success: function(res) {
        wx.setClipboardData({
          data: src,
          success: function(res) {}
        })
      }
    })
  },
})