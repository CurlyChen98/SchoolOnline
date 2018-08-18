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
    an1: '',
    an2: '',
    an3: '',
    anMove: -50,
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
    this.setData({
      cid: cid,
      lastDate: lastDay + "-" + mon + "-" + day,
      today: year + "-" + mon + "-" + day,
      befDay: befDay + "-" + mon + "-" + day,
    })
    wxrequest(this);
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

  arrorder: function(e) {
    let move = this.data.anMove;
    let op = 1;
    if (move == 50) {
      move = 0;
      op = 0;
    }

    let animation = wx.createAnimation({
      duration: 500,
      timingFunction: "ease",
    })
    let an1 = animation.opacity(op).translateY(move * 1).step().export();
    let an2 = animation.opacity(op).translateY(move * 2).step().export();
    let an3 = animation.opacity(op).translateY(move * 3).step().export();

    this.setData({
      an1: an1,
      an2: an2,
      an3: an3,
      anMove: this.data.anMove * (-1)
    })
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
      if (res.data.talk == "Ok") {
        that.setData({
          arrTopic: res.data.topic
        })
      } else {
        that.setData({
          arrTopic: ''
        })
      }
    }
  })
}