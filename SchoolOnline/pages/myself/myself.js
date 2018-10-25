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
      success: function(res) {
        wx.setClipboardData({
          data: src,
          success: function(res) {}
        })
      }
    })
  },
  // 注销
  zhuxiao: function() {
    let uid = wx.getStorageSync("uid");
    console.log(uid)
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "zhuxiao",
        uid: uid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res)
        if (res.data.talk == "Ok") {
          common.showToast("注销成功，下载登陆需要重新输入信息", "none", 3000, true);
        } else {
          common.showToast("未知的错误", "none", 1000, true);
        }
      }
    })
  },

})

function checkLevel() {
  let ulevel = wx.getStorageSync('ulevel');
  ulevel = String(ulevel)
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