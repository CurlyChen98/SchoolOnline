// myself.js

const app = getApp();

Page({

  data: {
    superhidden: true,
    superkey: '',
    key:'',
    uanme: '',
    rlevel:'',
    uclass:'',
  },

  onLoad: function () {
    let uname = wx.getStorageSync('uname');
    let ulevel = wx.getStorageSync('ulevel');
    let rlevel = '';
    let uclass = wx.getStorageSync('uclass');
    switch (ulevel){
      case "1":
        rlevel = "学生";
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
    this.setData({
      uanme: uname,
      rlevel: rlevel,
      uclass: uclass,
    })
    console.log("打开我的");
  },

  jump: function(e) {
    let url = e.currentTarget.dataset.type;
    wx.navigateTo({
      url: url + "/" + url
    })
  },

  superCheck: function() {
    this.setData({
      superhidden: false,
    })
    console.log("长按身份")
  },

  // 老师密匙输入框失去焦点方法
  superinput: function(e) {
    let keys = e.detail.value;
    this.setData({
      superkey: keys,
      key:'',
    })
  },

  // 老师密匙验证框提交触发方法
  confirm: function() {
    this.setData({
      superhidden: true,
    })
    console.log("提交老师密匙：" + this.data.superkey)
  },

})