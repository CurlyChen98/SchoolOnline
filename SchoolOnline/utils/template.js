// template.js

// 操作model框
function model(that, modalHidden, modalTitle) {
  that.setData({
    modalHidden: modalHidden,
    modalTitle: modalTitle,
  });
};

// 展示loading框
function showLoading(title, mask) {
  wx.showLoading({
    title: title,
    mask: mask,
  });
};

// 隐藏loading框
function hideLoading() {
  wx.hideLoading();
};

// 展示消息框
function showToast(title, icon, time, mask) {
  wx.showToast({
    title: title,
    icon: icon,
    duration: time,
    mask: mask,
  })
}

// 关闭当前页面跳转到另一个页面
function redirectTo(url) {
  wx.redirectTo({
    url: url
  })
}

// 防抖处理
function throttle(fn, gapTime) {
  if (gapTime == null || gapTime == undefined) {
    gapTime = 1500
  }

  let _lastTime = null
  return function() {
    let _nowTime = +new Date()
    if (_nowTime - _lastTime > gapTime || !_lastTime) {
      fn.apply(this, arguments)
      _lastTime = _nowTime
    } else {
      console.log("操作太快了")
    }
  }
}

// 设置导航栏标题
function setNav(title) {
  wx.setNavigationBarTitle({
    title: title,
  })
}

// 暴露借口
module.exports = {
  model,
  showLoading,
  hideLoading,
  showToast,
  throttle,
  redirectTo,
  setNav,
};