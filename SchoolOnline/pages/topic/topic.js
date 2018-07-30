Page({

  data: {
    selectdate: '',
    lastdate: '',
    order: 0,
    arrorder: ["倒叙", "顺序"],
    arrquer: [
      ["第一讲", [{
          id: 0,
          quer: '为什么第三条边大于第一条边加第二条边之和',
          like: 22,
          classMate: '王小明',
          date: '9/21',
        },
        {
          id: 1,
          quer: '勾股定理疑问',
          like: 98,
          classMate: '莉文东',
          date: '5/21',
        },
        {
          id: 2,
          quer: '勾股定理疑问',
          like: 98,
          classMate: '莉文东',
          date: '5/21',
        },
      ]],
      ["第er讲", [{
          id: 3,
          quer: 'css的弹性盒子不理解',
          like: 5,
          classMate: '老王',
          date: '12/31',
        },
        {
          id: 4,
          quer: 'css的弹性盒子不理解',
          like: 5,
          classMate: '老王',
          date: '12/31',
        },
      ]],
    ],
  },

  onLoad: function(options) {
    let nowdate = new Date()
    let year = nowdate.getFullYear();
    let mon = nowdate.getMonth() + 1;
    let day = nowdate.getDate();
    // console.log(nowdate);
    // console.log(year + "-" + mon + "-" + day)
    if (mon < 10)
      mon = "0" + mon
    if (day < 10)
      day = "0" + day
    let lastday = year + 1
    this.setData({
      selectdate: year + "-" + mon + "-" + day,
      lastdate: lastday + "-" + mon + "-" + day,
    })
  },

  onShow: function() {
    console.log("打开话题")
  },

  bindDateChange: function(e) {
    // console.log(e)
    this.setData({
      selectdate: e.detail.value
    })
  },

  bindPickerChange: function(e) {
    // console.log(e)
    this.setData({
      order: e.detail.value
    })
  },

  jumonote: function (e) {
    let tid = e.currentTarget.dataset.tid;
    // console.log("jump");
    // console.log(tid);
    wx.setStorageSync('tid', tid);
    wx.navigateTo({
      url: 'note/note',
    });
  },
})