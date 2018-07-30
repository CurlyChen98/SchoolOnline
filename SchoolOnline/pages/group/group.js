// group.js

Page({
  data: {
    arrtalk: [
      ['王小明', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz'],
      ['李二花', 'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssss'],
      ['三大爷', 'ss'],
    ],
    show: false,
  },

  onShow: function() {
    console.log("打开小组")
  },
  
  bindFormSubmit: function(e) {
    console.log(e.detail.value.text)
    let details = ['dfd', 'dssdsdsadsa'];
    let arr = this.data.arrtalk;
    arr.push(details)
    this.setData({
      arrtalk: arr
    })
  }
})