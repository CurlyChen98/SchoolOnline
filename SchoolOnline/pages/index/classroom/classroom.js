// classroom.js

Page({

  data: {
    cid: '',
    cname: '',
    www: "大萨达撒的撒多撒多萨达打打打萨达打大萨达撒的撒大萨达萨达撒的撒大萨达的撒大萨达大萨达撒发的说法的撒范大萨达撒的撒多撒多萨达打打打萨达打大萨达撒的撒大萨达萨达撒的撒大萨达的撒大萨达大萨达撒发的说法的撒范",
    videosrc: 'https://gslb.miaopai.com/stream/6yIb~rdCXWBtcNrLaPIZrZnCCgsq6s3azN~cSw__.mp4?ssig=25a0113ac6a3552988b44da98d5ddf71&time_stamp=1532598439132&cookie_id=&vend=1&os=3&partner=1&platform=2&cookie_id=&refer=miaopai&scid=6yIb%7ErdCXWBtcNrLaPIZrZnCCgsq6s3azN%7EcSw__',
    // videosrc: 'https://wantu-103o-tbm-hz.oss-cn-hangzhou.aliyuncs.com/nSvE8IgAiXYRL9wfOUU/9RvzTwQtWgrkdTdMPO4@@sd.mp4',
  },

  onLoad: function(options) {
    let cid = wx.getStorageSync('cid');
    let cname = wx.getStorageSync('cname');
    this.setData({
      cid: cid,
      cname: cname,
      // cname: '勾股定理的形成',
    })
  },

  onShow: function() {
    console.log("打开课程")
  },

  downloaddet: function() {

  },
})