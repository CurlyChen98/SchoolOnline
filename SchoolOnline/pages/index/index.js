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
    arrcourse: [{
        id: 0,
        courseName: '第一讲：大灰狼与小绵羊',
        courseDate: '9/31'
      },
      {
        id: 1,
        courseName: '第二讲：大灰狼与小绵羊',
        courseDate: '2/31'
      },
      {
        id: 2,
        courseName: '第三讲：大灰狼与小绵羊',
        courseDate: '10/31'
      },
      {
        id: 3,
        courseName: '第四讲：大灰狼与小绵羊',
        courseDate: '5/31'
      },
      {
        id: 4,
        courseName: '第五讲：大灰狼与小绵羊',
        courseDate: '10/31'
      },
      {
        id: 5,
        courseName: '第六讲：大灰狼与小绵羊',
        courseDate: '6/31'
      },
      {
        id: 6,
        courseName: '第七讲：大灰狼与小绵羊',
        courseDate: '12/31'
      },
      {
        id: 7,
        courseName: '第八讲：大灰狼与小绵羊',
        courseDate: '1/31'
      },
      {
        id: 8,
        courseName: '第九讲：大灰狼与小绵羊',
        courseDate: '11/31'
      },
    ],
  },

  onLoad: function(options) {
    let uid = wx.getStorageSync('uid');
    let uname = wx.getStorageSync('uname');
    let ulevel = wx.getStorageSync('ulevel');
    let uclass = wx.getStorageSync('uclass');
    if (uid != "" && uclass != "" && uname != "" && ulevel != "") {
      this.setData({
        classkey: uclass,
        studentkey: uname,
      })
    } else {
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
              let use = res.data.use;
              let talk = res.data.talk;
              if (talk == "NotHave" || use.s_uame == "") {
                that.setData({
                  classhidden: false,
                })
                if (use.s_uid == undefined) {
                  wx.setStorageSync('uid', use)
                } else {
                  wx.setStorageSync('uid', use.s_uid)
                }
              } else {
                wx.showTabBar();
                let uclass = res.data.class;
                that.setData({
                  classkey: uclass.s_cname,
                  studentkey: use.s_uame,
                })
                wx.setStorageSync('uid', use.s_uid)
                wx.setStorageSync('uclass', uclass.s_cname)
                wx.setStorageSync('uname', use.s_uame)
                wx.setStorageSync('ulevel', use.s_ulevel)
              }
            }
          })
        }
      });
    }
    console.log("打开首页（课堂）")
  },

  // 班级警告框输入框失去焦点事件
  classinput: function(e) {
    this.setData({
      classkey: e.detail.value,
      key: '',
    })
  },

  // 学生警告框输入框失去焦点事件
  studentinput: function(e) {
    this.setData({
      studentkey: e.detail.value,
      key: '',
    })
  },

  // 班级警告框点击事件
  classconfirm: function() {
    this.setData({
      classhidden: true,
      studenthidden: false,
    })
  },

  // 学生警告框点击事件
  studentconfirm: function() {
    this.setData({
      studenthidden: true,
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
        // 若班级密匙不正确重新输入
        if (res.data.talk == "NotRight") {
          wx.showModal({
            title: '警告',
            content: '班级密匙不存在',
            showCancel: false,
            confirmText: '重新输入',
            success: function(res) {
              that.setData({
                classhidden: false,
              })
            }
          })
        }
        // 正确则允许使用
        else {
          let use = res.data.use;
          let uclass = res.data.class;
          that.setData({
            classkey: uclass.s_cname,
            studentkey: use.s_uame,
          })
          wx.showTabBar();
          wx.setStorageSync('uid', use.s_uid)
          wx.setStorageSync('uclass', uclass.s_cname)
          wx.setStorageSync('uname', use.s_uame)
          wx.setStorageSync('ulevel', use.s_ulevel)
        }
      }
    })
  },

  // 点击触发课程事件
  jumpnext: function(e) {
    let cid = e.currentTarget.dataset.cid;
    let cname = e.currentTarget.dataset.cname;
    wx.setStorageSync('cid', cid);
    wx.setStorageSync('cname', cname);
    wx.navigateTo({
      url: 'classroom/classroom',
    });
  },
})