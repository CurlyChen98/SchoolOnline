//index.js

const app = getApp();
const common = require("../../../utils/template.js");

Page({

  data: {
    winWidth: 0,
    winHeight: 0,
    currentTab: 0,
    myTopic: [],
    myFollow: [],
    myUse: [],
    page: ['N', 'N'],
    uid: '',
  },

  onLoad: function() {
    let that = this;
    let uid = wx.getStorageSync('uid');
    this.setData({
      uid: uid,
    })
    getData(that, uid, "FindMyTopic")
    wx.getSystemInfo({
      success: function(res) {
        that.setData({
          winWidth: res.windowWidth,
          winHeight: res.windowHeight
        });
      }
    });
  },

  // 滑动切换页面
  bindChange: function(e) {
    var that = this;
    that.setData({
      currentTab: e.detail.current
    });
  },

  // 点击nav切换页面
  switchNav: function(e) {
    var that = this;
    if (this.data.currentTab === e.target.dataset.current) {
      return false;
    } else {
      that.setData({
        currentTab: e.target.dataset.current
      })
    }
  },

  // 删除帖子或跟帖
  deleteTopic: function(e) {
    let tid = e.currentTarget.dataset.tid;
    let thow = e.currentTarget.dataset.thow;
    let that = this;
    wx.showModal({
      title: '警告',
      content: "确认是否删除帖子!",
      success: function(res) {
        if (res.confirm) {
          wx.request({
            url: app.globalData.backAddress + app.globalData.backPage,
            data: {
              do: "DeleteTopic",
              tid: tid,
              thow: thow,
            },
            method: 'POST',
            header: {
              'content-type': 'application/x-www-form-urlencoded'
            },
            success: function(res) {
              if (res.data.talk == "Ok") {
                let uid = that.data.uid;
                if (thow =="talk"){
                  getData(that, uid, "FindMyTopic")
                } else if (thow == "talkdet"){
                  getData(that, uid, "FindMyFollow")
                }
              }else{
                common.showToast("未知的错误", "none", 1000, true);
              }
            }
          })
        }
      }
    })
  },

  swiperChange: function(e) {
    let that = this;
    let current = e.detail.current;
    let pages = this.data.page;
    that.setData({
      currentTab: current
    })
    if (pages[current] == 'N') {
      let uid = this.data.uid;
      switch (current) {
        case 0:
          getData(that, uid, "FindMyTopic")
          break;
        case 1:
          getData(that, uid, "FindMyFollow")
          break;
      }
    }
  },

  jump: function(e) {
    let taid = e.currentTarget.dataset.taid;
    wx.navigateTo({
      url: '../../topic/note/note?taid=' + taid,
    });
  },
})

// 请求数据
function getData(that, uid, does) {
  common.showLoading("加载中", true)
  let pages = that.data.page;
  let currentTabs = that.data.currentTab;
  pages[currentTabs] = 'Y';
  wx.request({
    url: app.globalData.backAddress + app.globalData.backPage,
    data: {
      do: does,
      uid: uid,
    },
    method: 'POST',
    header: {
      'content-type': 'application/x-www-form-urlencoded'
    },
    success: function(res) {
      common.hideLoading("加载中", true);
      switch (currentTabs) {
        case 0:
          that.setData({
            myTopic: res.data.topic,
            myUse: res.data.use,
            page: pages
          });
          break;
        case 1:
          that.setData({
            myFollow: res.data.topic,
            page: pages
          });
          break;
      }
    }
  })
}