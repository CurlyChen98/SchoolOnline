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
    key:'',
    downloadhidden: true,
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
    let task_src = this.data.task_src;
    let src = this.data.backAddress + "HomeworkDownload/" + task_src;
    let that = this;
    console.log(src)
    wx.downloadFile({
      url: src,
      success: function(res) {
        let temp = res.tempFilePath;
        // wx.openDocument({
        //   filePath: temp,
        //   success: function(res) {
        //   }
        // })
        wx.saveFile({
          tempFilePath: temp,
          success: function (res) {
            var savedFilePath = res.savedFilePath
            console.log(savedFilePath)
            wx.saveVideoToPhotosAlbum({
              filePath: savedFilePath,
              success(res) {
                console.log(res)
              }
            })
            // that.setData({
            //   key: savedFilePath,
            //   downloadhidden: false,
            // })
          }
        })
      }
    })
  },

  downloadconfirm:function(){
    this.setData({
      downloadhidden: true,
    })
  }
})