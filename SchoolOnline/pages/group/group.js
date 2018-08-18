// group.js

Page({
  data: {
    arrtalk: [
      ['Unknown', 'Unknown'],
    ],
    show: false,
  },

  onLoad: function(options) {
    let gid = wx.getStorageSync('gid')
    let cid = wx.getStorageSync('cid')
    let uid = wx.getStorageSync('uid')
    if (gid == "0") {
      wx.showModal({
        title: '警告',
        content: '暂未有小组',
        showCancel: false,
        confirmColor: "#03a8f3",
        success: function(res) {
          wx.reLaunch({
            url: "../myself/myself",
          })
        }
      })
      return;
    }
  },

  bindFormSubmit: function(e) {
    console.log(e.detail.value.text)
    let details = ['dfd', 'dssdsdsadsa'];
    let arr = this.data.arrtalk;
    arr.push(details)
    this.setData({
      arrtalk: arr
    })
  }
})