// classroom.js

const app = getApp();

Page({

  data: {
    backAddress: app.globalData.backAddress,
    backurl: 'Php/use.php',
    couid: '',
    title: '',
    content: '',
    video_src: '',
    task_src: '',
    date: '',
  },

  onLoad: function(options) {
    console.log("打开课程")
    let couid = wx.getStorageSync('couid');
    let back = this.data.backAddress + this.data.backurl;
    let that = this;
    wx.request({
      url: back,
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
        that.setData({
          couid: data.couid,
          title: data.title,
          content: data.content,
          video_src: data.video_src,
          task_src: data.task_src,
          date: data.date,
        })
      }
    })
  },

  homeworkDownload: function() {
    let task_src = this.data.task_src
    wx.downloadFile({
      url: task_src, 
      success: function(res) {
        let temp = res.tempFilePath;
        wx.saveFile({
          tempFilePath: temp,
          success: function (res) {
          }
        })
      }
    })
  }
})