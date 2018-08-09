// topic.js

const app = getApp();

Page({

  data: {
    selectDate: '',
    lastDate: '',
    befDay: '',
    today: '',
    order: 0,
    arrOrder: ["倒叙", "顺序"],
    arrTopic: '',
    cid: '',
  },

  onLoad: function(options) {
    let nowDate = new Date()
    let year = nowDate.getFullYear();
    let mon = nowDate.getMonth() + 1;
    let day = nowDate.getDate();
    if (mon < 10) mon = "0" + mon;
    if (day < 10) day = "0" + day;
    let lastDay = year + 1
    let befDay = year - 1
    let cid = wx.getStorageSync('cid');
    console.log(lastDay + "-" + mon + "-" + day)
    this.setData({
      cid: cid,
      lastDate: lastDay + "-" + mon + "-" + day,
      today: year + "-" + mon + "-" + day,
      befDay: befDay + "-" + mon + "-" + day,
    })
    wxrequest(this);
  },

  onShow: function() {
    console.log("打开话题")
  },

  dateChange: function(e) {
    this.setData({
      selectDate: e.detail.value,
      today: e.detail.value,
    })
    wxrequest(this);
  },

  orderChange: function(e) {
    this.setData({
      order: e.detail.value
    })
    wxrequest(this);
  },

  jumpnote: function(e) {
    let taid = e.currentTarget.dataset.taid;
    wx.navigateTo({
      url: 'note/note?taid=' + taid,
    });
  },

  jumpcreate: function(e) {
    wx.navigateTo({
      url: 'createNote/createNote',
    });
  },
})

function wxrequest(that) {
  let cid = that.data.cid;
  let order = that.data.order;
  if (order == 0) order = "DESC";
  else if (order == 1) order = "ASC";
  let selectDate = that.data.selectDate;
  wx.request({
    url: app.globalData.backAddress + app.globalData.backPage,
    data: {
      do: "FindTopic",
      cid: cid,
      order: order,
      day: selectDate,
    },
    method: 'POST',
    header: {
      'content-type': 'application/x-www-form-urlencoded'
    },
    success: function(res) {
      console.log(res.data)
      that.setData({
        arrTopic: res.data.topic
      })
    }
  })
}