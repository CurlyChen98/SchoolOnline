// node.js

const app = getApp();

Page({

  data: {
    backAddress: app.globalData.backAddress,
    backurl: 'Php/use.php',
    taid: '',
    arrdetail: '',
    topic:'',
  },

  onLoad: function(options) {
    console.log("进入帖子")
    let taid = wx.getStorageSync('taid');
    let back = this.data.backAddress + this.data.backurl;
    let that = this;
    wx.request({
      url: back,
      data: {
        do: "FindTalkDe",
        taid: taid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        let topicdet = res.data.topicdet;
        let topic = res.data.topic[0];
        that.setData({
          arrdetail: topicdet,
          topic: topic, 
          taid: taid,
        })
      }
    })
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
      success: function (res) {
        if (res.data.talk == "Ok") {
          wx.showToast({
            title: 'Follow成功',
            icon: 'success',
            duration: 1000,
            success: function () {
              setTimeout(function () {
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