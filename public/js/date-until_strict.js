// Parse dd/mm/yyyy nghiêm ngặt (không tự "normalize")
window.parseDMYStrict = function (s) {
  if (typeof s !== 'string') return null;
  var m = s.trim().match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (!m) return null;
  var d = +m[1], mo = +m[2], y = +m[3];
  var dt = new Date(y, mo - 1, d);
  if (dt.getFullYear() !== y || dt.getMonth() !== mo - 1 || dt.getDate() !== d) return null;
  return dt;
};

// Parse yyyy-mm-dd nghiêm ngặt
window.parseYMDStrict = function (s) {
  if (typeof s !== 'string') return null;
  var m = s.trim().match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!m) return null;
  var y = +m[1], mo = +m[2], d = +m[3];
  var dt = new Date(y, mo - 1, d);
  if (dt.getFullYear() !== y || dt.getMonth() !== mo - 1 || dt.getDate() !== d) return null;
  return dt;
};

// Đổi Date -> yyyy-mm-dd
window.toYMD = function (dt) {
  var y = dt.getFullYear();
  var m = String(dt.getMonth() + 1).padStart(2, '0');
  var d = String(dt.getDate()).padStart(2, '0');
  return y + '-' + m + '-' + d;
};

// yyyy-mm-dd hôm nay
window.todayYMD = function () {
  return window.toYMD(new Date());
};