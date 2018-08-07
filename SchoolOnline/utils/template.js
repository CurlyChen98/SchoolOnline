// template.js
function model(that, modalHidden, modalTitle) {
  that.setData({
    modalHidden: modalHidden,
    modalTitle: modalTitle,
  });
};

function showLoading(title, mask) {
  wx.showLoading({
    title: title,
    mask: mask,
  });
};

function hideLoading() {
  wx.hideLoading();
};

// 展示内容
module.exports = {
  model,
  showLoading,
  hideLoading,
};