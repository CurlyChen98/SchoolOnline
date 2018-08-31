// setsystem.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    modalHidden: true,
    modalTitle: '',
    modalKey: '',
    classList: '',
  },

  onLoad: function(options) {
    let that = this;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "ShowAllClass",
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data)
        that.setData({
          classList: res.data.class,
        });
      }
    })
  },

  onShow: function() {
    common.setNav("系统管理")
  },

  // 重置密码确认方法
  resetConfirmk:function(e){
    let cid = e.currentTarget.dataset.cid;
    let cname = e.currentTarget.dataset.cname;
    let that = this;
    wx.showModal({
      title: '警告',
      content: '确认是否重置' + cname + "密码!",
      success: function (res) {
        if (res.confirm) {
        }
      }
    })
  },

  deleteConfirmk: function(e) {
    let cid = e.currentTarget.dataset.cid;
    let cname = e.currentTarget.dataset.cname;
    let that = this;
    wx.showModal({
      title: '警告',
      content: '确认是否删除' + cname + "!",
      success: function(res) {
        if (res.confirm) {
          that.deleteClass(cid);
        }
      }
    })
  },

  deleteClass: function(cid) {
    let that = this;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "DeleteClass",
        cid: cid,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        console.log(res.data)
        if (res.data.talk == "Ok") {
          that.onLoad();
          common.showToast("删除成功", "success", 1000, true)
        } else {
          common.showToast("删除失败", "none", 1000, true)
        }
      }
    })
  },

  createClass: function() {
    common.model(this, false, "请输入班级名称");
  },

  cancel: function() {
    common.model(this, true, "")
  },

  confirm: function() {
    common.model(this, true, "")
    let modalkeyInput = '';
    let that = this;

    let pro = function(obj) {
      return new Promise(function(resolve, reject) {
        let query = wx.createSelectorQuery()
        query.select('#modalInput').fields({
          properties: ['value'],
        }, function(res) {
          modalkeyInput = res.value;
          that.setData({
            modalKey: '',
          })
          resolve();
        });
        query.exec();
      })
    }
    let pro1 = pro()
    pro1.then(function(data) {
      console.log(modalkeyInput)
      if (modalkeyInput == "") {
        common.showToast("没任何输入", "none", 1000, true);
      } else {
        wx.request({
          url: app.globalData.backAddress + app.globalData.backPage,
          data: {
            do: "CreateClass",
            claName: modalkeyInput,
          },
          method: 'POST',
          header: {
            'content-type': 'application/x-www-form-urlencoded'
          },
          success: function(res) {
            console.log(res.data)
            let data = res.data;
            if (data.talk == "Have") {
              common.showToast(res.data.error, "none", 1000, true);
            } else if (data.talk == "Ok") {
              that.onLoad();
              common.showToast("创建成功", "success", 1000, true);
            } else {
              common.showToast(res.data.error, "none", 1000, true);
            }
          }
        })
      }
    })
  },

})