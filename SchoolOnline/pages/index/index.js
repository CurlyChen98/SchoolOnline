// index.js

const app = getApp();

Page({

  data: {
    backAddress: app.globalData.backAddress,
    backurl: 'Php/use.php',
    classhidden: true,
    studenthidden: true,
    classkey: '',
    studentkey: '',
    key: '',
    arrcourse: '',
  },

  onLoad: function(options) {
    console.log("打开首页（课堂）")
    wx.hideTabBar();
    let back = this.data.backAddress + this.data.backurl;
    let that = this;
    wx.login({
      success: function(res) {
        let code = res.code;
        wx.request({
          url: back,
          data: {
            do: "CreateUse",
            code: code
          },
          method: 'POST',
          header: {
            'content-type': 'application/x-www-form-urlencoded'
          },
          success: function(res) {
            if (res.data.talk == "NotHave") {
              that.setData({
                classhidden: false,
              })
              wx.setStorageSync("uid", res.data.uid);
            } else if (res.data.talk == "Have") {
              LoadHave(res.data, that);
            }
          }
        })
      }
    });
  },

  // 班级警告框输入框失去焦点事件
  classinput: function(e) {
    this.setData({
      classkey: e.detail.value,
    })
  },

  // 学生警告框输入框失去焦点事件
  studentinput: function(e) {
    this.setData({
      studentkey: e.detail.value,
    })
  },

  // 班级警告框点击事件
  classconfirm: function() {
    this.setData({
      classhidden: true,
      studenthidden: false,
      key: '',
    })
  },

  // 学生警告框点击事件
  studentconfirm: function() {
    this.setData({
      studenthidden: true,
      key: '',
    })
    // 填写班级和姓名后发回服务器根据uid修改对应用户信息
    let back = this.data.backAddress + this.data.backurl;
    let that = this;
    let uid = wx.getStorageSync('uid')
    wx.request({
      url: back,
      data: {
        do: "UpdateNameClass",
        uid: uid,
        classkey: that.data.classkey,
        studentkey: that.data.studentkey,
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: function(res) {
        if (res.data.talk == "NotRight") {
          wx.showModal({
            title: '警告',
            content: '班级密匙错误',
            showCancel: false,
            success: function(res) {
              that.setData({
                classhidden: false,
              })
            }
          })
        } else if (res.data.talk == "Right") {
          LoadHave(res.data, that);
        }
      }
    })
  },

  // 点击触发课程事件
  jumpnext: function(e) {
    let couid = e.currentTarget.dataset.couid;
    wx.setStorageSync('couid', couid);
    wx.navigateTo({
      url: 'classroom/classroom',
    });
  },

})

// 正常用户输出信息方法
function LoadHave(data, that) {
  wx.showTabBar();
  let use = data.use[0];
  let uclass = data.class[0];
  let course = data.course;
  that.setData({
    classkey: uclass.name,
    studentkey: use.name,
    arrcourse: course,
  })
  wx.setStorageSync("cid", uclass.cid);
  wx.setStorageSync("cname", uclass.name);
  wx.setStorageSync("uid", use.uid);
  wx.setStorageSync("uanme", use.name);
  wx.setStorageSync("ulevel", use.level);
  wx.setStorageSync("gid", use.gid);
}