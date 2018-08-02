// group.js

Page({
  data: {
    arrtalk: [
      ['王小明', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz'],
      ['李二花', 'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssss'],
      ['三大爷', 'ss'],
    ],
    show: false,
  },

  onLoad: function(options) {
    console.log("打开小组讨论");
    let gid = wx.getStorageSync('gid')
    let cid = wx.getStorageSync('cid')
    let uid = wx.getStorageSync('uid')
    if (gid == "0") {
      wx.showModal({
        title: '警告',
        content: '暂未有小组',
        showCancel: false,
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