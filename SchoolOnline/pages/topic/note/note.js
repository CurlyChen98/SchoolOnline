// node.js

const app = getApp();

Page({

  data: {
    backAddress: app.globalData.backAddress,
    backurl: 'Php/use.php',
    taid: '',
    arrdetail: [{
      detail: "跟帖内容",
      name: '名字',
      date: '8/21'
    }, {
      detail: "跟帖内容",
      name: '名字',
      date: '8/21'
    }, ]
  },

  onLoad: function(options) {
    console.log("进入帖子")
    let taid = wx.getStorageSync('taid');
    let back = this.data.backAddress + this.data.backurl;
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
        console.log(res.data)
      }
    })
  },

  bindFormSubmit: function(e) {
    let intext = e.detail.value.text;
    let arr = this.data.arrdetail;
    let myarr = {
      detail: intext,
      name: '添加人',
      date: '8/21'
    }
    arr.push(myarr);
    this.setData({
      arrdetail: arr,
    })
  },
})