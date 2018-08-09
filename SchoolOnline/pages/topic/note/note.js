// node.js

const app = getApp();

Page({

  data: {
    taid: '',
    arrdetail: '',
    topic: '',
  },

  onLoad: function(options) {
    let taid = options.taid;
    let that = this;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "FindTopicDe",
        taid: taid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data)
        let topicdet = res.data.topicdet;
        let topic = res.data.topic;
        that.setData({
          arrdetail: topicdet,
          topic: topic,
          taid: taid,
        })
      }
    })
  },

  onShow: function() {
    console.log("进入帖子")
  },

  bindFormSubmit: function(e) {
    let detail = e.detail.value.text;
    let uid = wx.getStorageSync('uid');
    let taid = this.data.taid;
    let back = this.data.backAddress + this.data.backurl;
    let that = this;
    if (detail == "") {
      wx.showToast({
        title: '没有任何输入',
        icon: 'none',
        duration: 1000,
      })
      return;
    }
    wx.request({
      url: back,
      data: {
        do: "FollowTalk",
        taid: taid,
        uid: uid,
        detail: detail,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        if (res.data.talk == "Ok") {
          wx.showToast({
            title: 'Follow成功',
            icon: 'success',
            duration: 1000,
            success: function() {
              setTimeout(function() {
                wx.redirectTo({
                  url: '../note/note'
                })
              }, 1000)
            }
          })
        } else {
          wx.showToast({
            title: 'Follow失败,请咨询开发者',
            icon: 'none',
            duration: 1000,
          })
        }
      }
    })
  },
})