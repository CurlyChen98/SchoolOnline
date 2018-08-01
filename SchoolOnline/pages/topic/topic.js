// topic.js

const app = getApp();

Page({

  data: {
    backAddress: app.globalData.backAddress,
    backurl: 'Php/use.php',
    selectdate: '',
    lastdate: '',
    befday: '',
    today: '',
    order: 0,
    arrorder: ["倒叙", "顺序"],
    arrquer: '',
  },

  onLoad: function(options) {
    console.log("打开话题")
    let nowdate = new Date()
    let year = nowdate.getFullYear();
    let mon = nowdate.getMonth() + 1;
    let day = nowdate.getDate();
    if (mon < 10) mon = "0" + mon;
    if (day < 10) day = "0" + day;
    let lastday = year + 1
    let befday = year - 1
    this.setData({
      selectdate: year + "-" + mon + "-" + day,
      today: year + "-" + mon + "-" + day,
      lastdate: lastday + "-" + mon + "-" + day,
      befday: befday + "-" + mon + "-" + day,
    })

    let that = this;
    wxrequest(that);
  },

  DateChange: function(e) {
    this.setData({
      selectdate: e.detail.value
    })
    let that = this;
    wxrequest(that);
  },

  PickerChange: function(e) {
    this.setData({
      order: e.detail.value
    })
    let that = this;
    wxrequest(that);
  },

  jumpnote: function(e) {
    let taid = e.currentTarget.dataset.taid;
    wx.setStorageSync('taid', taid);
    wx.navigateTo({
      url: 'note/note',
    });
  },

  jumpcreate: function(e) {
    wx.navigateTo({
      url: 'createNote/createNote',
    });
  },
})

function wxrequest(that) {
  let back = that.data.backAddress + that.data.backurl;
  let cid = wx.getStorageSync('cid');
  let order = that.data.order;
  if (order == 0) order = "DESC";
  else if (order == 1) order = "ASC";
  let selectdate = that.data.selectdate;
  let today = that.data.today;
  let day = '';
  if (selectdate != today) day = selectdate;
  wx.request({
    url: back,
    data: {
      do: "FindTalk",
      cid: cid,
      order: order,
      day: day,
    },
    method: 'POST',
    header: {
      'content-type': 'application/x-www-form-urlencoded'
    },
    success: function(res) {
      that.setData({
        arrquer: res.data.topic
      })
    }
  })
}