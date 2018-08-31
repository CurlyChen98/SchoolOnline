// setsystem.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    modalHidden: true,
    ShowClassHidden: true,
    ShowClassTeacherkey: '',
    ShowClassClasskey: '',
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
        that.setData({
          classList: res.data.class,
          ShowClassTeacherkey: '',
          ShowClassClasskey: '',
        });
      }
    })
  },

  onShow: function() {
    common.setNav("系统管理")
  },

  // 班级信息模态框显示
  showClass: function(e) {
    let cIndex = e.currentTarget.dataset.index;
    let classList = this.data.classList;
    this.setData({
      ShowClassHidden: false,
      ShowClassTeacherkey: classList[cIndex].teacherkey,
      ShowClassClasskey: classList[cIndex].classkey,
    })
  },

  // 班级信息阻止隐藏
  ShowClassStop: function() {},

  // 班级信息模态框隐藏
  ShowClassHidden: function(e) {
    this.setData({
      ShowClassHidden: true,
    })
  },

  // 重置密码确认方法
  resetCodeConfirm: function(e) {
    let cid = e.currentTarget.dataset.cid;
    let cname = e.currentTarget.dataset.cname;
    let that = this;
    wx.showModal({
      title: '警告',
      content: "确认是否重置'" + cname + "'密码!",
      success: function(res) {
        if (res.confirm) {
          that.resetCode(cid);
        }
      }
    })
  },

  // 重置密码提交方法
  resetCode: function(e) {
    let that = this;
    wx.request({
      url: app.globalData.backAddress + app.globalData.backPage,
      data: {
        do: "ResetClassCode",
        cid: e,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        if (res.data.talk == "Ok") {
          that.onLoad();
          common.showToast("重置成功", "success", 1000, true)
        } else {
          common.showToast("重置失败", "none", 1000, true)
        }
      }
    })
  },

  // 删除班级确认方法
  deleteConfirm: function(e) {
    let cid = e.currentTarget.dataset.cid;
    let cname = e.currentTarget.dataset.cname;
    let that = this;
    wx.showModal({
      title: '警告',
      content: "确认是否删除'" + cname + "'!",
      success: function(res) {
        if (res.confirm) {
          that.deleteClass(cid);
        }
      }
    })
  },

  // 删除班级提交方法
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
        if (res.data.talk == "Ok") {
          that.onLoad();
          common.showToast("删除成功", "success", 1000, true)
        } else {
          common.showToast("删除失败", "none", 1000, true)
        }
      }
    })
  },

  // 班级模态框显示
  createClass: function() {
    common.model(this, false, "请输入班级名称");
  },

  // 班级模态框取消
  cancel: function() {
    common.model(this, true, "")
  },

  // 班级模态框确认
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