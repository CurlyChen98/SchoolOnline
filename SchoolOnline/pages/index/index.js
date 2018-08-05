// index.js

const app = getApp();

Page({

  data: {
    classhidden: true,
    studenthidden: true,
    classkey: '',
    studentkey: '',
    key: '',
    arrcourse: '',
  },

  onLoad: function(options) {
    console.log("打开课程")
    let back = app.globalData.backAddress;
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
            console.log(res.data)
            if (res.data.talk == "NotHave") {
              wx.reLaunch({
                url: '../login/login'
              })
            } else if (res.data.talk == "Have") {
              let data = res.data;
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
          }
        })
      }
    });
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