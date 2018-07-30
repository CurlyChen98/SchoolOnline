// setting.js

Page({

  data: {
    classkeyhidden: true,
    classkey: '',
    key: '',
  },

  onShow: function() {
    console.log("打开设置")
  },

  enterkey: function(e) {
    this.setData({
      classkeyhidden: false,
    })
  },

  // 班级密匙输入框失去焦点方法
  classinput: function(e) {
    let keys = e.detail.value;
    this.setData({
      classkey: keys,
      key: '',
    })
  },

  // 班级密匙验证框提交触发方法
  confirm: function() {
    this.setData({
      classkeyhidden: true,
    })
    console.log("提交班级密匙：" + this.data.classkey)
  },
})