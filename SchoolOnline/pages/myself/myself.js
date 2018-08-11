// myself.js

const app = getApp();
const common = require("../../utils/template.js");

Page({

  data: {
    rlevel: '',
    uanme: '',
    cname: '',
  },

  onLoad: function() {
    let uname = wx.getStorageSync('uname');
    let cname = wx.getStorageSync('cname');
    let rlevel = checkLevel();
    this.setData({
      uanme: uname,
      rlevel: rlevel,
      cname: cname,
    })
  },

  onShow: function() {
    console.log("打开我的");
    let rlevel = checkLevel();
    this.setData({
      rlevel: rlevel,
    })
  },

  jump: function(e) {
    let url = e.currentTarget.dataset.type;
    if (url == "mygroup") {
      wx.showToast({
        title: '暂未开放',
        icon: 'success',
        duration: 1000,
        mask: true,
      })
      return;
    }
    wx.navigateTo({
      url: url + "/" + url
    })
  },

  // 上传文件功能
  upLoadFiles: function(e) {
    let uid = wx.getStorageSync('uid');
    let cid = wx.getStorageSync('cid');
    let src = app.globalData.backAddress + app.globalData.backUploadJob + "?cid=" + cid + "&uid=" + uid;
    wx.showModal({
      title: '提示',
      content: '微信不支持该链接，确认复制链接到浏览器打开下载',
      showCancel: false,
      confirmColor: "#03a8f3",
      success: function(res) {
        wx.setClipboardData({
          data: src,
          success: function(res) {}
        })
      }
    })
  },

})

function checkLevel() {
  let ulevel = wx.getStorageSync('ulevel');
  let rlevel = 'Unknown';
  switch (ulevel) {
    case "1":
      rlevel = "学生";
      break;
    case "2":
      rlevel = "组长";
      break;
    case "3":
      rlevel = "学委";
      break;
    case "5":
      rlevel = "老师";
      break;
    case "10":
      rlevel = "管理员";
      break;
    case "100":
      rlevel = "开发者";
      break;
  }
  return rlevel;
}