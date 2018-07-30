// node.js

Page({

  data: {
    tid: '',
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
    let tid = wx.getStorageSync('tid');
    this.setData({
      tid: tid,
    })
    console.log(this.data.tid)
  },

  onShow: function() {
    console.log("进入帖子")
  },

  bindFormSubmit: function (e) {
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