(function ($) {
  $.fn.visible = function (partial) {
    var $t = $(this),
      $w = $(window),
      viewTop = $w.scrollTop(),
      viewBottom = viewTop + $w.height(),
      _top = $t.offset().top,
      _bottom = _top + $t.height(),
      compareTop = partial === true ? _bottom : _top,
      compareBottom = partial === true ? _top : _bottom;
    return compareBottom <= viewBottom && compareTop >= viewTop;
  };
})(jQuery);
(function (factory) {
  if (typeof define === "function" && define.amd) {
    // AMD (Register as an anonymous module)
    define(["jquery"], factory);
  } else if (typeof exports === "object") {
    // Node/CommonJS
    module.exports = factory(require("jquery"));
  } else {
    // Browser globals
    factory(jQuery);
  }
});
function isMobileScreen() {
  return window.innerWidth < 992;
}
function convertJsonDate(param) {
  try {
    let _timeString = param.substr(6, 13);
    let currentTime = new Date(parseInt(_timeString));
    let month = currentTime.getMonth() + 1;
    let day = currentTime.getDate();
    let year = currentTime.getFullYear();
    let hour = currentTime.getHours();
    let minute = currentTime.getMinutes();
    let date = day + "/" + month + "/" + year + " " + hour + ":" + minute;
    return date;
  } catch {
    return "";
  }
}
function friendlyTitle(str) {
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/giu, "a");
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/giu, "e");
  str = str.replace(/ì|í|ị|ỉ|ĩ/giu, "i");
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/giu, "o");
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/giu, "u");
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/giu, "y");
  str = str.replace(/đ/g, "d");
  str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
  str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/giu, "E");
  str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
  str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/giu, "O");
  str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/giu, "U");
  str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/giu, "Y");
  str = str.replace(/Đ/giu, "D");
  str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, ""); // huyền, sắc, hỏi, ngã, nặng
  str = str.replace(/\u02C6|\u0306|\u031B/g, ""); // mũ â (ê), mũ ă, mũ ơ (ư)
  str = str.replace(/\(|\)/giu, "");
  str = str.replace(/\./giu, "-");
  str = str.replace(/ /giu, "-");
  str = str.replace(/--/giu, "-");
  return str.replace("--", "-").toLowerCase();
}
function replaceTitle(str1) {
  str1 = str1.replace("(", "\\(");
  str1 = str1.replace(")", "\\)");
  str1 = str1.replace(".", "\\.");
  return str1;
}
function openPopup(url) {
  let width = 575,
    height = 400,
    left = document.documentElement.clientWidth / 2 - width / 2,
    top = (document.documentElement.clientHeight - height) / 2,
    opts =
      "status=1,resizable=yes" +
      ",width=" +
      width +
      ",height=" +
      height +
      ",top=" +
      top +
      ",left=" +
      left;
  win = window.open(url, "", opts);
  win.focus();
  return win;
}
function copyToClipboard(element) {
  let $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).attr("data-href")).select();
  document.execCommand("copy");
  $temp.remove();
  alert("Link đã được copy");
  return false;
}

function getDates() {
  let d = new Date();
  let strDate = d.getDate() + "/" + (d.getMonth() + 1) + "/" + d.getFullYear();
  return strDate;
}
function numberWithCommas(convertx) {
  return convertx.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function validateEmail(email) {
  return email.match(
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  );
}
function formatTime(time) {
  try {
    let min = Math.floor(time / 60);
    let sec = Math.floor(time % 60);
    return min + ":" + (sec < 10 ? "0" + sec : sec);
  } catch {
    return "";
  }
}
function decodeHtmlEntity(str) {
  return str.replace(/&#(\d+);/g, function (match, dec) {
    return String.fromCharCode(dec);
  });
}
function isPlaying(audelem) {
  return !audelem.paused;
}
function closePopUp() {
  $(".popUp").removeClass("active");
}
function timeSince(date) {
  try {
    let _date = new Date(date);
    let seconds = Math.floor((new Date() - _date) / 1000);
    let interval = seconds / 31536000;

    if (interval > 1) return `${Math.floor(interval)} năm trước`;
    interval = seconds / 2592000;
    if (interval > 1) return `${Math.floor(interval)} tháng trước`;
    interval = seconds / 86400;
    if (interval > 1)
      return interval < 2 ? "hôm qua" : `${Math.floor(interval)} ngày trước`;
    interval = seconds / 3600;
    if (interval > 1) return `${Math.floor(interval)} giờ trước`;
    interval = seconds / 60;
    if (interval > 1) return `${Math.floor(interval)} phút trước`;
    return `${Math.floor(interval)} giây trước`;
  } catch {
    return "";
  }
}
function getWeatherCity() {
  if ($(".onecms__weathercity").length == 0) return false;

  const isMobile = window.innerWidth < 992;

  const weekDays = function (days) {
    try {
      const currentTime = new Date(days);
      let day = currentTime.getDay();
      let dayNames = [
        "Chủ Nhật",
        "Thứ Hai",
        "Thứ Ba",
        "Thứ Tư",
        "Thứ Năm",
        "Thứ Sáu",
        "Thứ Bảy",
      ];
      if (isMobile)
        dayNames = ["CN", "T.Hai", "T.Ba", "T.Tư", "T.Năm", "T.Sáu", "T.Bảy"];
      const utc = new Date().toJSON().slice(0, 10);
      if (days.indexOf(utc) > -1) {
        if (isMobile) return "H.nay";
        return "Hôm nay";
      }
      return dayNames[day];
    } catch {
      return;
    }
  };

  // config default
  let weatherConfigByHour = {
    isShowDisplay_Y: false,
    isShowDisplay_X: false,
    isShowLabel: true,
    isShowWeatherIcon: true,
    topLapbel: 55,
    topIcon: 50,
    iconWidthHeight: 40,
    leftLabel: 20,
    leftIcon: 35,
  };

  let weatherConfigByDay = {
    isShowDisplay_Y: false,
    isShowDisplay_X: false,
    isShowLabel: true,
    isShowWeatherIcon: true,
    topLapbel: 50,
    topIcon: 50,
    iconWidthHeight: 40,
    leftLabel: 10,
    leftIcon: 38,
  };

  if (isMobile) {
    weatherConfigByHour = {
      isShowDisplay_Y: false,
      isShowDisplay_X: false,
      isShowLabel: true,
      isShowWeatherIcon: true,
      topLapbel: 40,
      topIcon: 35,
      iconWidthHeight: 30,
      leftLabel: 18,
      leftIcon: 33,
    };

    weatherConfigByDay = {
      isShowDisplay_Y: false,
      isShowDisplay_X: false,
      isShowLabel: true,
      isShowWeatherIcon: true,
      topLapbel: 40,
      topIcon: 40,
      iconWidthHeight: 30,
      leftLabel: 18,
      leftIcon: 30,
    };
  }

  let renderChartWeather = function () {
    const dataCity = $(".onecms__weathercity").attr("data-city");

    if (dataCity === undefined) return false;

    const url = `/api/getweather/${dataCity}`;

    $.ajax({
      url: url,
      type: "GET",
      success: function (_data) {
        try {
          if (_data == null) return false;
          _data = JSON.stringify(_data);
          _data = JSON.parse(_data);
          _data = JSON.parse(_data);
          if (_data.length === 0) return false;

          // meta
          $("h1.c-cat-list__current").html(`Thời tiết ${_data.CityName}`);
          $("title").html(`Thời tiết ${_data.CityName}`);
          $(".onecms__weathercity")
            .prepend(`<div class='box-info-weather flexbox' id='overview' style='min-height: 240px;'>
                                                                        <div class='box-info-weather__left'>
                                                                            <span class='weather-day-current'>Hiện tại</span>
                                                                            <div class='weather-day'>
                                                                                <img class='ic ic-weather' src='${_data.Currtent.ConditionIcon}' alt='weather logo'>
                                                                                <div class='big-temp'>${_data.Currtent.TempC}°</div>
                                                                                <div class='name'>${_data.Currtent.ConditionText}</div>
                                                                            </div>
                                                                            <div class='color-gray-2'>
                                                                                <p>Cao: ${_data.ForecastDays[0].MaxTempC}°  Thấp: ${_data.ForecastDays[0].MinTempC}°</p>
                                                                                <p>Xác suất mưa: ${_data.Currtent.WillItRain}%</p>
                                                                                <p>Gió: ${_data.Currtent.WindKph} Km/h</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class='box-info-weather__right text-right color-gray-2'>
                                                                            <div class='weather-tooltip mb40'>
                                                                                <span class='weather-tooltip-group'>
                                                                                    <span>Cảm giác như ${_data.Currtent.FeelsLikeC}°</span>
                                                                                    <svg class='ic ic-help'>
                                                                                        <use xlink:href='#icon-help'></use>
                                                                                    </svg>
                                                                                </span>
                                                                                <div class='box-info-hover'>
                                                                                    <div class='title'>
                                                                                        <span class='header_tooltip'>
                                                                                            <svg class='ic ic-help'>
                                                                                                <use xlink:href='#icon-temperature'></use>
                                                                                            </svg><span>Nhiệt độ cảm nhận</span>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class='scroll-height'>
                                                                                        <p>Nhiệt độ cảm nhận (heat index) là nhiệt độ cơ thể con người cảm thấy trong thực tế, được tính dựa trên dữ liệu nhiệt độ kết hợp với độ ẩm.</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <p>Độ ẩm: ${_data.Currtent.Humidity}%</p>
                                                                            <p>Tầm nhìn: ${_data.Currtent.VisKm} Km</p>
                                                                            <div>
                                                                                <span>UV:</span>
                                                                                <div class='weather-tooltip'>
                                                                                    <span class='weather-tooltip-group'>
                                                                                        <span>${_data.Currtent.Uv} / 11</span>
                                                                                        <svg class='ic ic-help'>
                                                                                            <use xlink:href='#icon-help'></use>
                                                                                        </svg>
                                                                                    </span>
                                                                                    <div class='box-info-hover'>
                                                                                        <div class='title'>
                                                                                            <span class='header_tooltip'>
                                                                                                <svg class='ic ic-help'>
                                                                                                    <use xlink:href='#icon-uv'></use>
                                                                                                </svg><span>Chỉ số UV</span>
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class='scroll-height'>
                                                                                            <ul class='chi-so'>
                                                                                                <li class='item quality-1'>
                                                                                                    <div class='lbl flex'>
                                                                                                        <div>1 → 2</div>
                                                                                                        <div>Thấp</div>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <li class='item quality-2'>
                                                                                                    <div class='lbl flex'>
                                                                                                        <div>3 → 5</div>
                                                                                                        <div>Trung bình</div>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <li class='item quality-3'>
                                                                                                    <div class='lbl flex'>
                                                                                                        <div>6 → 7</div>
                                                                                                        <div>Cao</div>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <li class='item quality-4'>
                                                                                                    <div class='lbl flex'>
                                                                                                        <div>8 → 10</div>
                                                                                                        <div>Rất cao</div>
                                                                                                    </div>
                                                                                                </li>
                                                                                                <li class='item quality-5'>
                                                                                                    <div class='lbl flex'>
                                                                                                        <div>11+</div>
                                                                                                        <div>Cực điểm</div>
                                                                                                    </div>
                                                                                                </li>
                                                                                            </ul>
                                                                                            <p>
                                                                                                Theo Cơ quan Bảo vệ Môi trường Mỹ (EPA), chỉ số UV dao động 0-2 được xem là thấp, chỉ số 8-10 có thời gian tiếp xúc gây bỏng là 25 phút. Chỉ số UV từ 11 trở lên được xem là cực kỳ cao, rất nguy hiểm, nguy cơ làm tổn thương da, mắt bị bỏng nếu tiếp xúc
                                                                                                ánh nắng mặt trời trong khoảng 15 phút mà không được bảo vệ.
                                                                                            </p>
                                                                                            <p>Tiếp xúc quá mức với ánh sáng mặt trời trong thời gian ngắn sẽ gây bỏng nắng, tổn thương mắt như đục thủy tinh thể, da bị bỏng, khô, sạm, tạo nếp nhăn, lão hóa nhanh. Tiếp xúc tia UV lâu dài, tích lũy có thể gây ung thư da.</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>`);

          // weather today
          const dataToday = _data.ForecastDays[0].WeatherHours.filter(function (
            currentValue,
            index
          ) {
            if (index % 3 === 0 || index == 23) {
              let time = currentValue.Time.slice(-5);
              if (time.startsWith("0")) time = time.slice(-4);
              currentValue.x = time;
              return currentValue;
            }
          });
          const listIconByHour = dataToday.map((x) => x.ConditionIcon);
          const listTempByHour = dataToday.map((x) => x.TempC);

          // plugin
          const showIconWeatherByHour = {
            id: "showIconWeatherByHour",
            beforeDraw(chart, args, options) {
              if (weatherConfigByHour.isShowWeatherIcon) {
                const {
                  ctx,
                  chartArea: { top, bottom, left, right, width, height },
                  scales: { x, y },
                } = chart;
                ctx.save();
                for (let i = 0; i <= listTempByHour.length - 1; i++) {
                  const icon = new Image();
                  icon.src = listIconByHour[i];
                  ctx.drawImage(
                    icon,
                    x.getPixelForValue(i) - weatherConfigByHour.leftIcon / 2,
                    y.getPixelForValue(listTempByHour[i]) -
                    weatherConfigByHour.topIcon,
                    weatherConfigByHour.iconWidthHeight,
                    weatherConfigByHour.iconWidthHeight
                  );
                }
              }
            },
          };
          const topLabelsByHour = {
            id: "topLabelsByHour",
            afterDatasetDraw(chart, args, options) {
              if (weatherConfigByHour.isShowLabel) {
                const {
                  ctx,
                  chartArea: { top, bottom, left, right, width, height },
                  scales: { x, y },
                } = chart;
                for (let i = 0; i <= listTempByHour.length - 1; i++) {
                  let temp = `${listTempByHour[i]}°C`;
                  if (isMobile) temp = `${listTempByHour[i]}°`;
                  ctx.fillText(
                    temp,
                    x.getPixelForValue(i) - weatherConfigByHour.leftLabel / 2,
                    y.getPixelForValue(listTempByHour[i]) -
                    weatherConfigByHour.topLapbel
                  );
                }
              }
            },
          };

          // config chart
          const dataWeatherByHour = {
            datasets: [
              {
                label: "Nhiệt độ (°C)",
                backgroundColor: "rgb(255,244,228)",
                borderColor: "#ff0000",
                data: dataToday,
                fill: true,
                tension: 0.4,
                stack: "combined",
                type: "line",
                parsing: {
                  xAxisKey: "x",
                  yAxisKey: "TempC",
                },
              },
            ],
          };
          const configWeatherByHour = {
            type: "line",
            data: dataWeatherByHour,
            plugins: [showIconWeatherByHour, topLabelsByHour],
            options: {
              interaction: {
                intersect: false,
                mode: "index",
              },
              maintainAspectRatio: false,
              plugins: {
                title: {
                  display: isMobile ? false : true,
                  text: "Thời tiết 24h hôm nay",
                  position: "bottom",
                },
                legend: {
                  position: "bottom",
                },
                tooltip: {
                  enabled: true,
                  callbacks: {
                    footer: function (context) {
                      const weatherByHour = context[0].dataset.data.filter(
                        (item) => item.x == context[0].label
                      );
                      if (weatherByHour.length === 0) return false;

                      const newData = [];
                      newData.push(`${weatherByHour[0].ConditionText}`);
                      newData.push(
                        `Cảm giác như ${weatherByHour[0].FeelsLikeC}°C`
                      );
                      newData.push(`Tầm nhìn xa: ${weatherByHour[0].VisKm}Km`);
                      newData.push(
                        `Xác suất mưa: ${weatherByHour[0].WillItRain}%`
                      );
                      newData.push(`Gió: ${weatherByHour[0].WindKph}Km/h`);
                      newData.push(`Độ ẩm: ${weatherByHour[0].Humidity}%`);
                      newData.push(`Uv: ${weatherByHour[0].Uv}/11`);
                      return newData;
                    },
                  },
                },
              },
              scales: {
                y: {
                  min: 0,
                  max: 50,
                  grid: {
                    //display: false,
                  },
                  display: weatherConfigByHour.isShowDisplay_Y,
                },
                x: {
                  grid: {
                    display: weatherConfigByHour.isShowDisplay_X,
                  },
                  //display: false,
                },
              },
            },
          };
          const chartWeatherByHour = new Chart(
            document.getElementById("Chart_WeatherToday"),
            configWeatherByHour
          );

          // weather by day

          const data7days = _data.ForecastDays.map((item) => {
            item.x = weekDays(item.Date);
            if (isMobile) item.x = weekDays(item.Date);
            return item;
          });
          const listIconByDay = data7days.map((x) => x.ConditionIcon);
          const listTempByDay = data7days.map((x) => x.MaxTempC);

          // plugin
          const showIconWeatherByDay = {
            id: "showIconWeatherByDay",
            beforeDraw(chart, args, options) {
              if (weatherConfigByDay.isShowWeatherIcon) {
                const {
                  ctx,
                  chartArea: { top, bottom, left, right, width, height },
                  scales: { x, y },
                } = chart;
                ctx.save();
                for (let i = 0; i <= listTempByDay.length - 1; i++) {
                  const icon = new Image();
                  icon.src = listIconByDay[i];
                  ctx.drawImage(
                    icon,
                    x.getPixelForValue(i) - weatherConfigByDay.leftIcon / 2,
                    y.getPixelForValue(listTempByDay[i]) -
                    weatherConfigByDay.topIcon,
                    weatherConfigByDay.iconWidthHeight,
                    weatherConfigByDay.iconWidthHeight
                  );
                }
              }
            },
          };
          const topLabelsByDay = {
            id: "topLabelsByDay",
            afterDatasetDraw(chart, args, options) {
              if (weatherConfigByDay.isShowLabel) {
                const {
                  ctx,
                  chartArea: { top, bottom, left, right, width, height },
                  scales: { x, y },
                } = chart;
                for (let i = 0; i <= listTempByHour.length - 1; i++) {
                  let temp = `${listTempByDay[i]}°C`;
                  if (isMobile) temp = `${listTempByDay[i]}°`;
                  ctx.fillText(
                    temp,
                    x.getPixelForValue(i) - weatherConfigByDay.leftLabel / 2,
                    y.getPixelForValue(listTempByDay[i]) -
                    weatherConfigByDay.topLapbel
                  );
                }
              }
            },
          };

          // config chart
          const dataWeatherByDay = {
            //labels: labelsWeatherByDay,
            datasets: [
              {
                label: "Nhiệt độ cao nhất (°C)",
                backgroundColor: "#ff0000",
                borderColor: "#ff0000",
                data: data7days,
                fill: false,
                tension: 0.4,
                stack: "combined",
                type: "line",
                parsing: {
                  xAxisKey: "x",
                  yAxisKey: "MaxTempC",
                },
              },
              {
                label: "Nhiệt độ thấp nhất (°C)",
                backgroundColor: "rgb(142,123,255)",
                borderColor: "rgb(142,123,255)",
                data: data7days,
                fill: false,
                type: "line",
                parsing: {
                  xAxisKey: "x",
                  yAxisKey: "MinTempC",
                },
              },
            ],
          };
          const configWeatherByDay = {
            type: "line",
            data: dataWeatherByDay,
            plugins: [showIconWeatherByDay, topLabelsByDay],
            options: {
              interaction: {
                intersect: false,
                mode: "index",
              },
              maintainAspectRatio: false,
              plugins: {
                title: {
                  display: true,
                  text: "Thời tiết 7 ngày tới",
                  position: "bottom",
                },
                legend: {
                  position: "bottom",
                },
                tooltip: {
                  enabled: true,
                  callbacks: {
                    footer: function (context) {
                      const weatherByDay = context[0].dataset.data.filter(
                        (item) => item.x == context[0].label
                      );
                      if (weatherByDay.length === 0) return false;
                      const newData = [];
                      newData.push(`${weatherByDay[0].ConditionText}`);
                      newData.push(
                        `Nhiệt độ trung bình: ${weatherByDay[0].AvgTempC}°C`
                      );
                      newData.push(
                        `Xác suất mưa: ${weatherByDay[0].DailyWillItRain}%`
                      );
                      newData.push(`Độ ẩm: ${weatherByDay[0].AvgHumidity}%`);
                      newData.push(`Uv: ${weatherByDay[0].Uv}/11`);
                      return newData;
                    },
                  },
                },
              },
              scales: {
                y: {
                  min: 0,
                  max: 50,
                  grid: {
                    //display: false,
                  },
                  display: weatherConfigByHour.isShowDisplay_Y,
                  //ticks: {
                  //    font: {
                  //        size: 20
                  //    }
                  //}
                },
                x: {
                  grid: {
                    display: weatherConfigByHour.isShowDisplay_Y,
                  },
                },
              },
            },
          };
          const chartWeatherByDay = new Chart(
            document.getElementById("Chart_WeatherByDay"),
            configWeatherByDay
          );
        } catch (err) {
          console.log(err.message);
        }
      },
      error: function (errorMessage) {
        console.log("error" + errorMessage);
      },
    });
  };

  renderChartWeather();
}
function getWeather() {
  let _dataAllWeather = {};
  let loadDataWeather = function () {
    if ($(".onecms__weather").length == 0) return false;
    $.ajax({
      url: "/api/getweather",
      type: "get",
      success: function (_data) {
        try {
          if (_data == null) return false;
          _data = JSON.stringify(_data);
          _data = JSON.parse(_data);
          _data = JSON.parse(_data);
          if (_data.length === 0) return false;
          $(".onecms__weather").append(`<div id="widget-weather">
                                                <ul class="current-city"></ul>
                                                <div class="filter">
                                                    <input placeholder="Nhập tìm tỉnh thành …">
                                                    <div class="city-list">
                                                        <div class="choosed-city"></div>
                                                        <div class="others-city">
                                                            <p>Tỉnh thành khác</p>
                                                            <ul></ul>
                                                            <div class="no-result hide" style="display:none"><i class="ti-info-alt"></i> Không tìm thấy kết quả</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`);

          if ($(".onecms__weather__thisplace").length > 0) {
            $(".onecms__weather__thisplace")
              .append(`<div class="weather" id="weather-box">
                                    <div class="weather__today">
                                        <select class="form-control" id="weather-location">
                                            
                                        </select>
                                    </div>
                                    <ul class="weather__listing" id="listing"> 
                                    </ul>
                                </div>`);
          }
          _dataAllWeather = _data;
          let _current_city, data_weather_current;
          if (localStorage.getItem("_city")) {
            _current_city = window.localStorage.getItem("_city");
          }

          if (_current_city !== undefined)
            data_weather_current = _dataAllWeather.find(
              (x) =>
                x.CityId == _current_city ||
                x.CityId.replace("-", "") == _current_city
            );

          if (data_weather_current === undefined) {
            if (localStorage.getItem("_city")) {
              localStorage.removeItem("_city");
            }
            const defaultCity = "ha-noi";
            data_weather_current = _dataAllWeather.find(
              (x) =>
                x.CityId == defaultCity ||
                x.CityId.replace("-", "") == defaultCity
            );
          }

          // weather top
          if (data_weather_current !== undefined) {
            $(".current-city").append(
              `<li><strong>${data_weather_current.CityName}</strong><span><em>${data_weather_current.Currtent.TempC}°C</em> / ${data_weather_current.Forecast.MinTempC} - ${data_weather_current.Forecast.MaxTempC}°C</span><img src="${data_weather_current.Currtent.ConditionIcon}" alt=""><i class="ti-arrow-circle-down"></i></li>`
            );
            $(".choosed-city").append(
              `<p>Đang hiển thị</p><ul><li>${data_weather_current.CityName}<span>${data_weather_current.Currtent.TempC}°C<img src="${data_weather_current.Currtent.ConditionIcon}" alt=""></span></li></ul>`
            );
          }

          // use custom weather this place
          if ($(".onecms__weather__thisplace").length > 0) {
            // weather bottom
            let htmlWeatherCurrent = `<div class="weather__city"><div class="weather__status" id="today-ic"><img src="${data_weather_current.Currtent.ConditionIcon}"></div>
                                        <div class="weather__info">
                                            <div class="weather__temp" id="today-temp">${data_weather_current.Currtent.TempC}°C</div>
                                            <div class="weather__range" id="today-range">${data_weather_current.Forecast.MinTempC}°C - ${data_weather_current.Forecast.MaxTempC}°C</div>
                                        </div>
                                        <div class="weather__desc" id="today-status">${data_weather_current.Currtent.ConditionText}</div></div>`;
            $(".onecms__weather__thisplace select").after(htmlWeatherCurrent);
            $(window).ready(function () {
              $(".onecms__weather__thisplace select")
                .val(data_weather_current.CityId)
                .change();
            });
          }

          // use weather by site
          if ($(".onecms__weather__district").length > 0) {
            const htmlWeather = `<strong>${data_weather_current.CityName}</strong>
											 <span><em>${data_weather_current.Currtent.TempC}°C</em> / ${data_weather_current.Forecast.MinTempC}-${data_weather_current.Forecast.MaxTempC}°C</span>
											 <img src="${data_weather_current.Currtent.ConditionIcon}" alt="${data_weather_current.Currtent.CityName}">`;
            $(".onecms__weather__district").append(htmlWeather);
          }

          // sort
          _dataAllWeather.sort(function (a, b) {
            try {
              const nameA = a.CityId.toUpperCase();
              const nameB = b.CityId.toUpperCase();
              if (nameA < nameB) {
                return -1;
              }
              if (nameA > nameB) {
                return 1;
              }
            } catch {
              return 0;
            }

            return 0;
          });
          $.each(_dataAllWeather, function (idx, data_w) {
            let city1 = friendlyTitle(data_w.CityId).toLowerCase();
            city1 = city1.replace(new RegExp("-", "imug"), " ");
            $(".others-city ul").append(
              `<li city="${data_w.CityId.toLowerCase()}" city1="${city1}">${data_w.CityName
              }<span>${data_w.Currtent.TempC}°C<img src="${data_w.Currtent.ConditionIcon
              }" alt=""></span></li>`
            );
            if ($(".onecms__weather__thisplace").length > 0) {
              $("#weather-location").append(
                `<option value="${data_w.CityId}">${data_w.CityName}</option>`
              );
            }
          });
        } catch (err) {
          console.log(err.message);
        }
      },
      error: function (errorMessage) {
        console.log("error" + errorMessage);
      },
    });
  };
  function SeachWeather(input) {
    if (input === undefined) return false;
    input = input.trim();
    $(".others-city ul li").each(function () {
      if (
        input == "" ||
        $(this).attr("city").indexOf(input) != -1 ||
        $(this).attr("city1").indexOf(input) != -1 ||
        $(this).text().toLowerCase().indexOf(input) != -1
      ) {
        $(this).css("display", "flex");
      } else {
        $(this).css("display", "none");
      }

      if ($('.others-city ul li[style="display: flex;"]').length === 0) {
        $(".no-result").css("display", "block");
      } else {
        $(".no-result").css("display", "none");
      }
    });
  }
  $(window).ready(function () {
    $(".onecms__weather").on("keyup", ".filter input", function () {
      SeachWeather($(this).val().toLowerCase());
    });
    $(".onecms__weather").on("click", ".current-city", function (e) {
      $("#widget-weather").addClass("expanded");
      e.stopPropagation();
    });
    $(document).click(function (e) {
      if (
        !$(e.target).is(".onecms__weather .filter, .onecms__weather .filter *")
      ) {
        $("#widget-weather").removeClass("expanded");
      }
    });
    $(".onecms__weather").on("click", ".others-city ul li", function () {
      const _city = $(this).attr("city");
      if (_city === undefined) return false;
      //_city = _city.replace(new RegExp(" ", "imug"), "");
      let data_weather_current = _dataAllWeather.find(
        (x) => x.CityId === _city
      );
      if (data_weather_current === undefined) return false;
      $(".current-city").html(
        `<li><strong>${data_weather_current.CityName}</strong><span><em>${data_weather_current.Currtent.TempC}°C</em> / ${data_weather_current.Forecast.MinTempC} - ${data_weather_current.Forecast.MaxTempC}°C</span><img src="${data_weather_current.Currtent.ConditionIcon}" alt=""><i class="ti-arrow-circle-down"></i></li>`
      );
      $(".choosed-city").html(
        `<p>Đang hiển thị</p><ul><li>${data_weather_current.CityName}<span>${data_weather_current.Currtent.TempC}°C<img src="${data_weather_current.Currtent.ConditionIcon}" alt=""></span></li></ul>`
      );
      $("#widget-weather").removeClass("expanded");
      window.localStorage.setItem("_city", _city);
      return false;
    });
    $(".onecms__weather__thisplace").on("change", "select", function (e) {
      let _city = $(".onecms__weather__thisplace select")
        .find(":selected")
        .val();
      if (_city === undefined) return false;
      _city = _city.replace(new RegExp(" ", "imug"), "");
      let data_weather_current = _dataAllWeather.find(
        (x) => x.CityId == _city || x.CityId.replace("-", "") == _city
      );

      if (data_weather_current === undefined) {
        $(".weather__city").html("");
        $(".weather__listing").html(
          '<li class="weather__day"><time class="weather__date">Không có thông tin thời tiết</time></div></li>'
        );
        return false;
      }

      $(".weather__status > img").attr(
        "src",
        data_weather_current.Currtent.ConditionIcon
      );
      $(".weather__temp").html(`${data_weather_current.Currtent.TempC}°C`);
      $(".weather__range").html(
        `${data_weather_current.Forecast.MinTempC}°C - ${data_weather_current.Forecast.MaxTempC}°C `
      );
      $(".weather__desc").html(
        `${data_weather_current.Currtent.ConditionText}`
      );
      window.localStorage.setItem("_city", _city);

      return false;
    });
  });

  loadDataWeather();
}
function shareSomeContent(title, text, url) {
  if (!navigator.share) {
    return;
  }

  navigator
    .share({ title, text, url })
    .then(() => { })
    .catch((error) => {
      console.error("Error sharing the content", error);
    });
}
function refreshTimeAgo(element) {
  element.each(function () {
    let dataTime = $(this).attr("datetime");
    if (dataTime) {
      $(this).html(timeAgo(dataTime));
    }
    return;
  });
}
function timeAgo(time) {
  switch (typeof time) {
    case "number":
      break;
    case "string":
      time = +new Date(time);
      break;
    case "object":
      if (time.constructor === Date) time = time.getTime();
      break;
    default:
      time = +new Date();
  }
  let time_formats = [
    [60, "s", 1], // 60
    [120, "1' trước", "1 phút tới"], // 60*2
    [3600, "'", 60], // 60*60, 60
    [7200, "1h trước", "1h tới"], // 60*60*2
    [86400, "h", 3600], // 60*60*24, 60*60
    [172800, "Hôm qua", "Ngày mai"], // 60*60*24*2
    [604800, " ngày", 86400], // 60*60*24*7, 60*60*24
    [1209600, "Tuần trước", "Tuần tới"], // 60*60*24*7*4*2
    [2419200, " tuần", 604800], // 60*60*24*7*4, 60*60*24*7
    [4838400, "Tháng trước", "Tháng tới"], // 60*60*24*7*4*2
    [29030400, " tháng", 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
    [58060800, "Năm trước", "Năm tới"], // 60*60*24*7*4*12*2
    [2903040000, " năm", 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
    //[5806080000, 'Thế kỷ trước', 'Thế kỷ tới'], // 60*60*24*7*4*12*100*2
    //[58060800000, 'thế kỷ', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
  ];
  let seconds = (+new Date() - time) / 1000,
    token = "trước",
    list_choice = 1;

  if (seconds == 0) {
    return "vừa xong";
  }
  if (seconds < 0) {
    seconds = Math.abs(seconds);
    token = "tới";
    list_choice = 2;
  }
  let i = 0,
    format;
  while ((format = time_formats[i++]))
    if (seconds < format[0]) {
      if (typeof format[2] == "string") return format[list_choice];
      else
        return Math.floor(seconds / format[2]) + "" + format[1] + " " + token;
    }
  return time;
}
(function () {
  if ($(".onecms__currentTime").length > 0) {
    const currentTime = new Date();
    let day = currentTime.getDay();
    const dayNames = [
      "Chủ Nhật",
      "Thứ Hai",
      "Thứ Ba",
      "Thứ Tư",
      "Thứ Năm",
      "Thứ Sáu",
      "Thứ Bảy",
    ];
    let year = currentTime.getFullYear().toString();
    let month = (currentTime.getMonth() + 1).toString();
    if (month.length < 2) {
      month = "0" + month;
    }
    let date = currentTime.getDate().toString();
    if (date.length < 2) {
      date = "0" + date;
    }
    const isMobileScreen = window.innerWidth < 992;
    //let hour = currentTime.getHours().toLocaleString("vi-VN");
    //if (hour.length < 2) {
    //    hour = "0" + hour;
    //}
    //const min = currentTime.getMinutes().toString();
    //if (min.length < 2) {
    //    min = "0" + min;
    //}
    if (isMobileScreen) {
      $(".onecms__currentTime").html(
        `<b>${dayNames[day]},</b> ${date}/${month}/${year}`
      );
      return;
    }
    $(".onecms__currentTime").html(
      `<p><b>${dayNames[day]},</b> ${date}/${month}/${year}</p>`
    );
    return;
  }
})();
(function () {
  if ($(".is-timeline").length === 0) return false;
  let element = $(".c-time-count span");
  refreshTimeAgo(element);

  //loop run
  const intervalTime = setInterval(function () {
    refreshTimeAgo(element);
  }, 1000 * 10);

  //Out time
  setTimeout(function () {
    clearInterval(intervalTime);
    console.log("clear");
  }, 1000 * 60 * 60);
})();
(function ($) {
  "use strict";
  $.fn.ScrolLoading = function (options) {
    let settings = $.extend(
      {
        trigger: false,
        container: $(this),
        selector: false,
        distance: 0,
        timeout: 2000,
        debug: false,
      },
      options
    );

    let scope = $(this);
    let isLoadingScroll = false;

    let log = function (obj) {
      if (settings.debug && console.log != undefined) {
        console.log(obj);
      }
    };
    log("initialized");
    log(scope);
    log(settings);

    $(this).on("click", function () {
      isLoadingScroll = false;
      log(isLoadingScroll);
    });

    //let timeOutInterval = setInterval(() => { isLoadingScroll = false }, settings.timeout);
    let timeOutInterval = setInterval(function () {
      isLoadingScroll = false;
      log("existed interval");
    }, settings.timeout);

    let i_Loading = 0;
    if (settings.distance) {
      $(window).on("scroll", function () {
        log(
          `LoadingScroll: ${isLoadingScroll}|WebLoading: ${WebControl.isLoading}`
        );
        //no data => clear
        if (WebControl.isLoading) {
          i_Loading++;
          if (i_Loading == 1) {
            log("clearInterval");
            clearInterval(timeOutInterval);
          }
          return false;
        }
        if (
          $(document).scrollTop() >=
          $(document).height() - $(window).height() - settings.distance
        ) {
          if (isLoadingScroll) return false;
          $(settings.trigger).trigger("click");
          isLoadingScroll = true;
        }
      });
    }
  };
})(jQuery);
function isMobile() {
  let check = false;
  (function (a) {
    if (
      /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(
        a
      ) ||
      /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
        a.substr(0, 4)
      )
    )
      check = true;
  })(navigator.userAgent || navigator.vendor || window.opera);
  return check;
}
function convertJsonDate(param) {
  let KKK = param.substr(6, 13);
  let currentTime = new Date(parseInt(KKK));
  let month = currentTime.getMonth() + 1;
  let day = currentTime.getDate();
  let year = currentTime.getFullYear();
  let hour = currentTime.getHours();
  let minute = currentTime.getMinutes();
  let date = day + "/" + month + "/" + year + " " + hour + ":" + minute;
  return date;
}
function frienly_title(str) {
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/giu, "a");
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/giu, "e");
  str = str.replace(/ì|í|ị|ỉ|ĩ/giu, "i");
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/giu, "o");
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/giu, "u");
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/giu, "y");
  str = str.replace(/đ/g, "d");
  str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
  str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/giu, "E");
  str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
  str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/giu, "O");
  str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/giu, "U");
  str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/giu, "Y");
  str = str.replace(/Đ/giu, "D");
  str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, ""); // huyền, sắc, hỏi, ngã, nặng
  str = str.replace(/\u02C6|\u0306|\u031B/g, ""); // mũ â (ê), mũ ă, mũ ơ (ư)
  str = str.replace(/\(|\)/giu, "");
  str = str.replace(/\./giu, "-");
  str = str.replace(/ /giu, "-");
  str = str.replace(/--/giu, "-");
  return str.replace("--", "-");
}
function change_title(str1) {
  str1 = str1.replace("(", "\\(");
  str1 = str1.replace(")", "\\)");
  str1 = str1.replace(".", "\\.");
  return str1;
}
function ClickPopup(url) {
  let width = 575,
    height = 400,
    left = document.documentElement.clientWidth / 2 - width / 2,
    top = (document.documentElement.clientHeight - height) / 2,
    opts =
      "status=1,resizable=yes" +
      ",width=" +
      width +
      ",height=" +
      height +
      ",top=" +
      top +
      ",left=" +
      left;
  win = window.open(url, "", opts);
  win.focus();
  return win;
}
function CopyToClipboard(element) {
  let $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).attr("data-href")).select();
  document.execCommand("copy");
  $temp.remove();
  alert("Link đã được copy");
  return false;
}
function getDates() {
  let d = new Date();
  let strDate = d.getDate() + "/" + (d.getMonth() + 1) + "/" + d.getFullYear();
  return strDate;
}
function numberWithCommas(convertx) {
  return convertx.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function validateEmail(email) {
  return email.match(
    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  );
}
function isScrolledIntoView(elem) {
  let docViewTop = $(window).scrollTop();
  let docViewBottom = docViewTop + $(window).height();

  let elemTop = $(elem).offset().top;
  let elemBottom = elemTop + $(elem).height();

  return elemBottom <= docViewBottom && elemTop >= docViewTop;
}
function getTimeString(second) {
  var ms = "";
  var m = Math.floor(second / 60);
  if (m < 10) {
    ms = "0" + m.toString();
  } else {
    ms = m.toString();
  }

  var ss = "";
  var sc = Math.floor(second % 60);
  if (sc < 10) {
    ss = "0" + sc.toString();
  } else {
    ss = sc.toString();
  }

  return ms + ":" + ss;
}
function isPlaying(audelem) {
  return !audelem.paused;
}
//global
var g_footer_height = $(".l-footer").height();
var g_nav_height = $(".l-nav").height();

//web control
var WebControl = WebControl || {};
WebControl.loadmore_params = () => ({
  type: "24h",
  keyword: "",
  publisherId: 0,
  channelId: 0,
  eventId: 0,
});
WebControl.isLoading = false;
WebControl.initChannelPage = function () {
  let $load_more_count = 0;
  let loadMore = function () {
    if (WebControl.isLoading) return false;
    let _data = WebControl.loadmore_params();
    $(".loading_img").show();
    $("#load_more").hide();
    let url = `/api/getMoreArticle/${_data.type}_${_data.keyword === "" ? "empty" : decodeHtmlEntity(_data.keyword)
      }_${_data.publisherId}_${_data.channelId}_${_data.eventId}`;
    if (_data.publisherId === undefined) return false;
    $.ajax({
      url: url,
      type: "get",
      success: function (data) {
        $(".loading_img").hide();
        if (data.length == 0) {
          WebControl.isLoading = true;
          return false;
        }
        $.each(data, function (idx, art) {
          let html =
            '<li class="loadArticle" pid="' +
            art.PublisherId +
            '">\
                                        <div class="b-grid">\
                                            <div class="b-grid__img"><a href="' +
            art.LinktoMe2 +
            '"><img src="' +
            (art.Thumbnail == ""
              ? "/Assets/images/placeholder-image10.jpg"
              : art.Thumbnail_540x360) +
            '" alt="' +
            art.Title +
            '" title="' +
            art.Title +
            '" /></a></div>\
                                            <div class="b-grid__content">\
                                                <div class="b-grid__row">\
                                                    <h3 class="b-grid__title"><a href="' +
            art.LinktoMe2 +
            '">' +
            art.Title +
            "</a></h3>\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </li>";
          $(".loadAjax:first").append(html);
        });
        $("#load_more").show();
        if (data.length < 15) {
          $("#load_more").hide();
        }
      },
    });
  };

  $("#load_more").click(function () {
    loadMore();
    return false;
  });

  // scroll for loadmore
  $(window).scroll(function () {
    if ($(window).scrollTop() === $(document).height() - $(window).height()) {
      if ($load_more_count < 3) {
        loadMore();
        $load_more_count++;
      }
    }
  });
};
WebControl.initDetailPage = function () {
  window.history.pushState(
    { urlPath: window.location.href },
    "",
    window.location.href
  );
  let checkload = 0;
  let loadMore = function () {
    let _data = WebControl.loadmore_params();
    if (_data.arrRelationId === undefined || _data.arrRelationId.length === 0) {
      WebControl.isLoading = true;
      return false;
    }
    let _publisherId = _data.arrRelationId[0];

    if (_publisherId === undefined || checkload === 3) return false;
    $("#overlay").show();
    const url = `/api/getmoredetail/${_publisherId}`;
    $.ajax({
      url: url,
      type: "get",
      success: function (data) {
        $("#overlay").hide();
        if (data.status === "error" || data.detailHtml === "") {
          checkload++;
          return false;
        }
        _data.arrRelationId.shift();
        $(".onecms__detail:last").after(data.detailHtml);
        checkload = 1;
        let _last = $(".onecms__detail:last");
        $("html, body").stop().animate(
          {
            scrollTop: _last.offset().top,
          },
          1500,
          "swing"
        );
        $.get("/script/ui-" + _publisherId + ".js", () => false);
        //audio
        $("audio").each(function () {
          if (isPlaying(this)) this.pause();
        });
        let _doc = document.documentElement.cloneNode();
        _doc.innerHTML = data.detailHtml;
        let _audioElement = $(_doc.querySelector("audio"));
        if (_audioElement.length === 0) return false;
        WebControl.Audio(_audioElement);
        //Khởi tạo lại js ở chi tiết bài viết khi load more theo bộ js từ editor mới
        initSnippetsCustomOnReady(".onecms__detail:last");
      },
    });
  };

  $("#load_more").click(function () {
    loadMore();
    return false;
  });
  $(document).scroll(function () {
    let cutoff = $(window).scrollTop();
    let cutoffRange = cutoff + g_nav_height; //435
    $(".onecms__detail").each(function () {
      if ($(this).offset().top + $(this).height() > cutoffRange) {
        let current__title = $(this).attr("data-title");
        let current__url = $(this).attr("data-url");
        if (current__title === undefined || current__url === undefined)
          return false;
        //$('.onecms__detail').removeClass("onecms__current")
        //$(this).addClass("onecms__current");
        let history__url = window.history.state?.urlPath
          ? window.history.state.urlPath
          : "";
        if (history__url !== undefined && history__url !== current__url) {
          document.title = current__title;
          window.history.pushState(
            { urlPath: current__url },
            current__title,
            current__url
          );
        }
        return false;
      }
    });
    //SlideEffect();
  });
  function SlideEffect() {
    const boxes = document.querySelectorAll(".onecms__detail");
    //if (boxes.length === 1) return false;
    const triggerBottom = (window.innerHeight / 5) * 4;
    //console.log(triggerBottom)
    boxes.forEach((box) => {
      const boxTop = box.getBoundingClientRect().top;
      //console.log(boxTop)
      if (boxTop < triggerBottom) {
        box.classList.add("show");
      } else {
        box.classList.remove("show");
      }
    });
    let current__title = $(".show:last").attr("data-title");
    let current__url = $(".show:last").attr("data-url");
    let history__url = window.history.state.urlPath;
    if (current__title === undefined || current__url === undefined)
      return false;
    if (history__url !== undefined && history__url !== current__url) {
      document.title = current__title;
      window.history.pushState(
        { urlPath: current__url },
        current__title,
        current__url
      );
    }

    return false;
  }
  //$('html, body').stop().animate({
  //    'scrollTop': $(".l-nav").offset().top
  //}, 500, 'swing');
  WebControl.Audio($("audio"));
};
WebControl.CommentDetailPage = function () {
  let $publisherId = WebControl.PublisherId;
  let $parentId = 0;
  let $sort_by = "like";
  let $row_num = 0;

  let $load_comment_first = true;

  let $cmt_name = "";
  let $cmt_email = "";
  let $cmt_content = "";
  let $cmt_parentId = "";

  let $FriendlyName = WebControl.FriendlyName;
  let $f_share = WebControl.f_share;
  let $g_share = WebControl.g_share;

  let loadComments = function () {
    $.ajax({
      url: "/api/getcomment",
      type: "post",
      data: {
        publisherid: $publisherId,
        parentid: $parentId,
        sort_by: $sort_by,
        row_num: $row_num,
      },
      success: function (data) {
        ////console.log(data);

        //$('.box-comment .bc-content').html('');
        $.each(data, function (idx, cmt) {
          //console.log('load parent');
          //console.log(cmt);

          if ($sort_by == "like") {
            $sort_like = cmt.Liked;
          } else {
            $sort_date = cmt.CreatedAt;
          }
          $(".contentCC:last").append(
            '<div class="b-grid itemboxC" row_num="' +
            cmt.RowNum +
            '"><div class="b-grid__content" parentid="' +
            cmt.CommentId +
            '">' +
            '<div class="b-grid__row"><span class="b-grid__title">' +
            cmt.Name +
            '</span> - <span class="b-grid__time">' +
            convertJsonDate(cmt.CreatedAt) +
            "</span></div>" +
            '<div class="b-grid__row b-grid__desc" id="cmt' +
            cmt.CommentId +
            '">' +
            cmt.Content +
            "</div>" +
            '<div class="b-grid__row">' +
            '<span class="b-grid__anwser tl-reply">Trả lời</span>' +
            '<span class="b-grid__like like" id="' +
            cmt.CommentId +
            '"><i class="icon16-heart"></i>Thích <span class="likeCount">' +
            cmt.Liked +
            "</span>" +
            '<a class="b-grid__share" href="' +
            $f_share +
            "#cmt-" +
            cmt.CommentId +
            '"><i class="icon24-facebook"></i>Chia sẻ</a>' +
            "</div>" +
            '<div class="c-comment-input comment-reply hidden">' +
            '<div class="form-group">' +
            '<textarea class="form-control txt-content" name="" placeholder="Vui lòng nhập tiếng việt có dấu"></textarea>' +
            '<label class="control-label help-block"><em></em></label> <br />' +
            '<a href="javascript:void(0)" class="btnSend btn-send-comment" parentid="' +
            cmt.CommentId +
            '">Gửi bình luận</a>' +
            "<span> </span>" +
            '<a class="btn-close-comment" href="javascript:void(0)" parentid="0">Đóng</a>' +
            "</div>" +
            "</div>" +
            '<div class="b-grid__sub itemboxC">' +
            "</div>" +
            "</div>" +
            "</div>" +
            "</div><!-- b-grid -->"
          );
          if (cmt.ChildComment.length > 0) {
            //$('.itemboxC:last').after('<div class="itemBox tl"></div>');
          }
          $.each(cmt.ChildComment, function (idx2, cmt2) {
            //console.log(cmt2);
            $(".itemboxC:last").append(
              '<div class="b-grid itemboxC" row_num="' +
              cmt.RowNum +
              '"><div class="b-grid__content" parentid="' +
              cmt.CommentId +
              '">' +
              '<div class="b-grid__row"><span class="b-grid__title">' +
              cmt2.Name +
              '</span> - <span class="b-grid__time">' +
              convertJsonDate(cmt2.CreatedAt) +
              "</span></div>" +
              '<div class="b-grid__row b-grid__desc" id="cmt' +
              cmt2.CommentId +
              '">' +
              cmt2.Content +
              "</div>" +
              '<div class="b-grid__row">' +
              '<span class="b-grid__anwser tl-reply">Trả lời</span>' +
              '<span class="b-grid__like like" id="' +
              cmt2.CommentId +
              '"><i class="icon16-heart"></i>Thích <span class="likeCount">' +
              cmt2.Liked +
              "</span></span>" +
              '<a class="b-grid__share" href="' +
              $f_share +
              "#cmt-" +
              cmt2.CommentId +
              '"><i class="icon24-facebook"></i>Chia sẻ</a>' +
              "</div>" +
              '<div class="c-comment-input comment-reply hidden">' +
              '<div class="form-group">' +
              '<textarea class="form-control txt-content" name="" placeholder="Vui lòng nhập tiếng việt có dấu"></textarea>' +
              '<label class="control-label help-block"><em></em></label> <br />' +
              '<a href="javascript:void(0)" class="btnSend btn-send-comment" parentid="' +
              cmt.CommentId +
              '">Gửi bình luận</a>' +
              "<span> </span>" +
              '<a class="btn-close-comment" href="javascript:void(0)" parentid="0">Đóng</a>' +
              "</div>" +
              "</div>" +
              "</div>" +
              "</div>" +
              "</div><!-- b-grid -->"
            );
          });
          // $('.box-comment .bc-content').append($comt)
          if (cmt.ChildComment.length == 3) {
            $(".box-comment .bc-content")
              .find(".ci-sub:last")
              .append(
                '<div class="bc-more"><a href="#" class="comment-load-more-child small">Xem thêm...</a></div> '
              );
          }
        });

        //first run
        if ($load_comment_first) {
          if (data.length > 0) {
            $(".box-comment .no-comment").addClass("hidden");
            $(".box-comment .parent-load-more").removeClass("hidden");
          } else {
            $(".box-comment .no-comment").removeClass("hidden");
            $(".box-comment .parent-load-more").addClass("hidden");
          }

          var $target = $(window.location.hash);
          if (
            $target != null &&
            $target != undefined &&
            $target.offset() != undefined
          ) {
            $("html, body")
              .stop()
              .animate(
                {
                  scrollTop: $target.offset().top,
                },
                500,
                "swing",
                function () { }
              );
          }
          $load_comment_first = false;
        }

        //waitingDialog.hide();
      },
    });
  };
  let sendComment = function () {
    let name = $.trim($cmt_name);
    let email = $.trim($cmt_email);
    let content = $.trim($cmt_content).replace(/\r\n|\r|\n/g, "<br/>");
    $.ajax({
      url: "/api/addcomment",
      type: "post",
      data: {
        p: $cmt_parentId,
        a: $publisherId,
        n: name,
        e: email,
        c: content,
      },
      success: function (data) {
        //console.log(data);
        data = JSON.parse(data);
        if (data.errorCode == 2) {
          alert("Bạn phải chờ sau 1 phút sau mới được tiếp tục gửi ý kiến !");
        } else {
          $(".txt-content").val("");
          $("#txtName").val("");
          $("#txtEmail").val("");
          $(".form").removeClass("has-error");
          $(".comment-item").find(".bc-input").addClass("hidden");
          $(".popUp.binhLuan").removeClass("active");
          $(".comment-reply").addClass("hidden");
          let $target_message = $(".commentForms");
          $(".message").removeClass("hidden");
          $("html, body")
            .stop()
            .animate(
              {
                scrollTop: $target_message.offset().top - 10,
              },
              300,
              "swing",
              function () { }
            );
        }
      },
    });
  };

  //load first top comment
  loadComments();
  $("li.comment-sort-by-like").click(function () {
    if ($(this).hasClass("active")) return false;
    $("li.comment-sort-by-newest").removeClass("active");
    $(this).addClass("active");

    $sort_by = "like";
    $row_num = 0;

    //waitingDialog.show();
    $(".contentCC").html("");
    loadComments();

    return false;
  });
  $("li.comment-sort-by-newest").click(function () {
    if ($(this).hasClass("active")) return false;
    $("li.comment-sort-by-like").removeClass("active");
    $(this).addClass("active");

    $sort_by = "date";
    $row_num = 0;

    //waitingDialog.show();
    $(".contentCC").html("");
    loadComments();

    return false;
  });

  $(".c-comments").on("click", ".like", function () {
    let _commentId = $(this).attr("id");
    let aaa = $(this);
    let like_val = aaa.find(".likeCount").text();
    let url = `/api/addlikecomment/${_commentId}`;
    $.ajax({
      url: url,
      type: "get",
      success: function (data) {
        data = JSON.parse(data);
        if (data.errorCode == 0) {
          $(this).addClass("active");

          aaa.find(".likeCount").html((parseInt(like_val) + 1).toString());
          alert(
            "Like thành công, sau vài phút ý kiến sẽ được cập nhật số lượng like !"
          );
          aaa.removeClass("like");
          return false;
        } else if (data.errorCode == 2) {
          alert("Bạn phải chờ sau 1 phút sau mới được tiếp tục like ý kiến !");
          return false;
        }
      },
    });
  });
  $(".c-comments").on("click", ".tl-reply", function () {
    let _commentId = $(this).closest(".b-grid__content").attr("parentid");
    $parentId = _commentId;
    //show commentbox
    $(this)
      .closest(".b-grid__content")
      .find(".comment-reply:first")
      .removeClass("hidden");
    return false;
  });
  $(".c-comments").on("click", ".btn-close-comment", function () {
    $(this).closest(".comment-reply").addClass("hidden");
    return false;
  });
  //send click
  $(".c-comments").on("click", ".btnSend", function () {
    let $txtContent = $(this)
      .closest(".c-comment-input")
      .find(".txt-content:first");
    $cmt_content = $txtContent.val();
    $cmt_parentId = $(this).attr("parentid");
    $txtContent.closest(".c-comment-input").removeClass("has-error");
    $txtContent.closest(".c-comment-input").find("em").html("");
    if ($cmt_content.length == 0) {
      $txtContent
        .closest(".c-comment-input")
        .addClass("has-error")
        .find("em")
        .html("Bạn chưa nhập nội dung ý kiến !");
      $(".txt-content").focus();
      return false;
    } else if ($cmt_content.length < 10) {
      $txtContent
        .closest(".c-comment-input")
        .addClass("has-error")
        .find("em")
        .html("Nội dung ý kiến quá ngắn !");
      return false;
    } else if ($cmt_content.length > 1000) {
      $txtContent
        .closest(".c-comment-input ")
        .addClass("has-error")
        .find("em")
        .html("Nội dung ý kiến quá dài !");
      return false;
    }
    //show input author
    $(".popUp.binhluancomment").addClass("active");
    return false;
  });

  $(".btnSendComment").on("click", function () {
    let $txtName = $("#txtName");
    let $txtEmail = $("#txtEmail");

    $cmt_name = $.trim($txtName.val());
    $cmt_email = $.trim($txtEmail.val());
    $("#binhluanmodal").find(".form").removeClass("has-error");
    $("#binhluanmodal").find("em").html("");
    if ($cmt_name.length == 0) {
      $txtName
        .closest(".box")
        .addClass("has-error")
        .find("em")
        .html("Bạn chưa nhập họ và tên !");
      return false;
    } else if ($cmt_email.length == 0) {
      $txtEmail
        .closest(".box")
        .addClass("has-error")
        .find("em")
        .html("Bạn chưa nhập địa chỉ email !");
      return false;
    }
    //send comment
    sendComment();
    return false;
  });

  //load more comment
  $(".c-comments").on("click", ".comment-load-more", function () {
    $parentId = 0;
    $row_num = $(".b-grid.itemboxC:last:last").attr("row_num");
    loadComments();

    return false;
  });
  // ++++++ load comment con //
  $(".box-comment").on("click", ".comment-load-more-child", function () {
    let that = this;
    $parentId = $(this).closest(".ci-sub").attr("parentid");
    $row_num = $(this)
      .closest(".ci-sub")
      .find(".comment-item:last")
      .attr("row_num");

    //waitingDialog.show();
    let url = `/api/getcomment/${$publisherId}_${$parentId}_${$sort_by}_${$row_num}`;
    $.ajax({
      url: url,
      type: "get",
      success: function (data) {
        //console.log(data);
        $.each(data, function (idx2, cmt2) {
          //console.log(cmt2);
          $(that).before(
            "" +
            '<div class="comment-item  grid" id="' +
            cmt2.CommentId +
            '" row_num="' +
            cmt2.RowNum +
            '">' +
            '<div class="img"><a href="javascript:;"><img src="https://btnmt.onecmscdn.com/assets/img/avatar.jpg" alt="' +
            cmt2.Name +
            '"></a></div>' +
            '<div class="g-content">' +
            '     <div class="ci-row g-row" id="cmt-' +
            cmt2.CommentId +
            '"><span class="ci-name g-title">' +
            cmt2.Name +
            '</span> - <span class="ci-time">' +
            formatJSONDate(cmt2.CreatedAt) +
            "</span></div>" +
            '     <div class="ci-row g-row">' +
            "          " +
            cmt2.Content +
            "" +
            "     </div>" +
            /////
            //+ '<div class="comment-item" id="' + cmt2.CommentId + '" row_num="' + cmt2.RowNum + '">'
            //+ ' <div class="ci-row" id="cmt-' + cmt2.CommentId + '"><span class="ci-name">' + cmt2.Name + '</span> - <span class="ci-time">' + formatJSONDate(cmt2.CreatedAt) + '</span></div>'
            //+ '     <div class="ci-row">'
            //+ '      ' + cmt2.Content + ''
            //+ '      </div>'
            "     </div>" +
            '     <div class="g-row">' +
            '             <span class="g-count btn-like"><i class="fa fa-thumbs-o-up"></i>' +
            cmt2.Liked +
            " Thích</span>" +
            '             <span class="g-comment btn-answer"><i class="fa fa-comments"></i>Trả lời</span>' +
            "     </div>" +
            " </div>" +
            "</div>"
          );
          //////////////////////
          //+ '  <div class="ci-row">'
          //+ '   <a class="btn-like" href="#">Thích <span>' + cmt2.Liked + '</span></a>'
          //+ '   <a class="btn-share social-share" href="' + $f_share + '#cmt-' + cmt2.CommentId + '" target="_blank"><i class="share-facebook"></i></a>'
          //+ '   <a class="btn-share social-share" href="' + $g_share + '#cmt-' + cmt2.CommentId + '" target="_blank"><i class="share-google-plus"></i></a>'
          //+ '  </div>'
          //+ '</div>');
        });
        //waitingDialog.hide();
      },
    });

    return false;
  });

  $(".box-news").on("click", "a.social-share", function () {
    let url = $(this).attr("href");
    ClickPopup(url);
    return false;
  });
};
WebControl.Answer = function () {
  let $publisherId = WebControl.PublisherId;
  let LABEL_CORRECT = [
    "Bạn là thần đồng",
    "Bạn rất xuất sắc",
    "Kiến thức của bạn không tồi",
    "Bạn có thể làm tốt hơn thế",
  ];
  let t = $("<p>", { id: "quiz_result" }).html(
    "<span class='message'>Hãy trả lời các câu hỏi để biết kết quả của bạn</span>"
  );
  if ($(".quiz-caption").length > 0) {
    $(".leftDetail .description").append(t);
  }

  let a = $(".quiz ul").length;
  $("#quiz_total").html(a);
  let s = 0,
    e = 0;
  let sendTraloicauhoi = function () {
    let name = $.trim($cmt_name);
    let email = $.trim($cmt_email);
    let Traloi =
      $(".correct.selected").length + "/" + $(".description .quiz").length;
    $.ajax({
      url: "/api/sendAnswer",
      type: "post",
      data: { p: $publisherId, n: name, e: email, t: Traloi },
      success: function (data) {
        //console.log(data);
        data = JSON.parse(data);
        if (data.errorCode == 2) {
          alert("Bạn phải chờ sau 1 phút sau mới được tiếp tục gửi ý kiến !");
        } else {
          let $target_message = $(".messagetl");
          $target_message.removeClass("hidden");
          $("html, body")
            .stop()
            .animate(
              {
                scrollTop: $(".formComment").offset().top,
              },
              300,
              "swing",
              function () { }
            );
        }
        $(".ketquatraloi #txtName").val("");
        $(".ketquatraloi #txtEmail").val("");
        $(".ketquatraloi .form").removeClass("has-error");
        $(".popUp.ketquatraloi").removeClass("active");
        $("#traloiketqua").css({ display: "none" });
      },
    });
  };
  $(".quiz li strong").each(function (t, a) {
    $(a).parents("  li").addClass("correct");
  });
  $(".quiz li").on("click", function (n) {
    n.preventDefault();

    let i = $(this).parents(".quiz");
    if (!i.hasClass("answered")) {
      if ((e++, $(this).hasClass("correct") && s++, e == a)) {
        t.append(
          '<span id="correct">' + s + '</span><span id="total">' + a + "</span>"
        );
        let c = (100 * s) / a;
        100 == c
          ? t.find(".message").html(LABEL_CORRECT[0])
          : c >= 80
            ? t.find(".message").html(LABEL_CORRECT[1])
            : c >= 50
              ? t.find(".message").html(LABEL_CORRECT[2])
              : t.find(".message").html(LABEL_CORRECT[3]);
        $("#quiz_result").append(
          '<span id="traloiketqua">Gửi kết quả trả lời</span>'
        );
      }
      i.addClass("answered"), $(this).addClass("selected");
      $("#traloiketqua").on("click", function (n) {
        n.preventDefault();
        $(".popUp.ketquatraloi").addClass("active");
        $(".ketquatraloi")
          .find(".kqtl")
          .html(
            '<span id="correct">' +
            $(".correct.selected").length +
            '</span><span id="total">' +
            $(".description .quiz").length +
            "</span>"
          );
        return false;
      });
      $(".btnTraloicauhoi")
        .off("click")
        .on("click", function () {
          let $txtName = $(".ketquatraloi #txtName");
          let $txtEmail = $(".ketquatraloi #txtEmail");
          $cmt_name = $.trim($txtName.val());
          $cmt_email = $.trim($txtEmail.val());
          $("#binhluanmodal").find(".form").removeClass("has-error");
          $("#binhluanmodal").find("em").html("");
          if ($cmt_name.length == 0) {
            $txtName
              .closest(".box")
              .addClass("has-error")
              .find("em")
              .html("Bạn chưa nhập họ và tên !");
            return false;
          } else if ($cmt_email.length == 0) {
            $txtEmail
              .closest(".box")
              .addClass("has-error")
              .find("em")
              .html("Bạn chưa nhập địa chỉ email !");
            return false;
          }
          //send comment
          sendTraloicauhoi();
          return false;
        });
    }
  });
};
WebControl.Audio = function (element_current, isLoad = true) {
  const _getAudioElement = (ele) => {
    if (ele === undefined || ele === null) return false;
    let _audioId = ele.attr("id");
    if (_audioId === undefined || _audioId === null) return false;
    return document.getElementById(`${_audioId}`);
  };

  $(document).ready(function () {
    $(".c-audio-box__play").click(function () {
      let _this = $(this).parent().find("audio");
      let _audioElement_Play = _getAudioElement(_this);
      if (_audioElement_Play === null || _audioElement_Play === false)
        return false;
      if (isPlaying(_audioElement_Play)) _audioElement_Play.pause();
      //pause all element before play
      $(".c-audio-box__play").css("display", "inline-block");
      $(".c-audio-box__pause").css("display", "none");
      $("audio").each(function () {
        if (isPlaying(this)) this.pause();
      });
      //play
      let promiseA = _audioElement_Play.play();
      if (promiseA !== undefined) {
        promiseA
          .then((_) => {
            _audioElement_Play.volume = 0.5;
            _audioElement_Play.play();
            $(this).css("display", "none");
            $(this)
              .parent()
              .find(".c-audio-box__pause")
              .css("display", "inline-block");
            let _thisCurrentTime = $(this)
              .closest(".c-audio-box")
              .find(".c-audio-box__time");
            _audioElement_Play.addEventListener("timeupdate", function () {
              _thisCurrentTime.text(
                getTimeString(_audioElement_Play.currentTime)
              );
            });
          })
          .catch((error) => {
            console.log(`error play audio`);
          });
      }
      return false;
    });

    $(".c-audio-box__pause").click(function () {
      let _this = $(this).parent().find("audio");
      let _audioElement_Pause = _getAudioElement(_this);
      if (_audioElement_Pause === null || _audioElement_Pause === false)
        return false;
      _audioElement_Pause.pause();
      $(this)
        .parent()
        .find(".c-audio-box__play")
        .css("display", "inline-block");
      $(this).css("display", "none");
      return false;
    });
  });
  //init audio
  let _audioLink = element_current.attr("data-src");
  if (_audioLink === undefined || _audioLink === "") return false;
  let _audioElement_Current = _getAudioElement(element_current);
  if (_audioElement_Current === null || _audioElement_Current === false)
    return false;
  //set link audio
  _audioElement_Current.setAttribute("src", _audioLink);
  //speed
  _audioElement_Current.playbackRate = 1.1;
  //get duration time
  _audioElement_Current.addEventListener("canplay", function () {
    let _currentId = element_current.attr("id");
    let cr_time_element = element_current
      .closest(".c-audio-box")
      .find(`.${_currentId.replace("fileAudio_", "onecms_current_time_")}`);
    if (cr_time_element.length === 0) return false;
    let duration = getTimeString(_audioElement_Current.duration);
    let element_duration = $(
      document.getElementsByClassName(cr_time_element[0].className)
    );
    element_duration.text(duration);
  });
  //eror
  _audioElement_Current.onerror = function () {
    let _currentId = element_current.attr("id");
    let cr_player_element = element_current.closest(
      `.${_currentId.replace("fileAudio_", "onecms_current_player_")}`
    );
    if (cr_player_element.length === 0) return false;
    cr_player_element.hide();
  };
  //select voice
  $(".onecms_select_audio").click(function () {
    $(".onecms_select_audio").each(function () {
      $(this).removeClass("active");
    });
    $(this).addClass("active");
    let _thisAudio = $(this).closest(".c-audio-box").find("audio");
    let dropdownAudioLink = $(this).attr("data-audio");
    $(".c-audio-box__dropdown").css("display", "none");
    setTimeout(function () {
      $(".c-audio-box__dropdown").removeAttr("style");
    }, 500);
    if (dropdownAudioLink === undefined || _thisAudio.length === 0)
      return false;
    _thisAudio.attr("data-src", dropdownAudioLink);
    let _audioElement_Play = _getAudioElement(_thisAudio);
    if (_audioElement_Play === null || _audioElement_Play === false)
      return false;
    if (
      _thisAudio.attr("src") === dropdownAudioLink &&
      isPlaying(_audioElement_Play) === true
    )
      return false;
    //set link
    if (_thisAudio.attr("src") !== dropdownAudioLink)
      _audioElement_Play.setAttribute("src", dropdownAudioLink);
    //pause all element before play
    if (isPlaying(_audioElement_Play)) _audioElement_Play.pause();
    $(".c-audio-box__play").css("display", "inline-block");
    $(".c-audio-box__pause").css("display", "none");
    $("audio").each(function () {
      if (isPlaying(this)) this.pause();
    });
    //play
    let promiseA = _audioElement_Play.play();
    if (promiseA !== undefined) {
      promiseA
        .then((_) => {
          _audioElement_Play.volume = 0.5;
          _audioElement_Play.play();
          $(this)
            .closest(".c-audio-box")
            .find(".c-audio-box__play")
            .css("display", "none");
          $(this)
            .closest(".c-audio-box")
            .find(".c-audio-box__pause")
            .css("display", "inline-block");
          let _thisCurrentTime = $(this)
            .closest(".c-audio-box")
            .find(".c-audio-box__time");
          _audioElement_Play.addEventListener("timeupdate", function () {
            _thisCurrentTime.text(
              getTimeString(_audioElement_Play.currentTime)
            );
          });
        })
        .catch((error) => {
          console.log(`error play audio`);
        });
    }
  });
};

$(document).ready(function () {
  $(".btnSearch").click(function () {
    let keyword = $(this).parent().find("input").val();
    if (keyword !== undefined && $.trim(keyword) != "") {
      window.location = "/search?q=" + keyword.replace(/\s/gi, "+");
    }
    return true;
  });
  $(".txt_keyword").keyup(function (evt) {
    if (evt.keyCode == 13 || evt.which == 13) {
      $(".btnSearch").trigger("click");
      return false;
    }
    return true;
  });
  $("#btnStocksSearch").click(function () {
    let keyword = $("#stocksInput").val();
    if ($.trim(keyword) != "") {
      window.location = "/stock/" + keyword.replace(/\s/gi, "+") + ".html";
    }
    return true;
  });
  $("#stocksInput").keyup(function (evt) {
    if (evt.keyCode == 13 || evt.which == 13) {
      $("#btnStocksSearch").trigger("click");
      return false;
    }
    return true;
  });
  $(".fb").click(function (e) {
    ClickPopup($(this).attr("href"));
    return false;
  });
  $(".copy__link").click(function (e) {
    e.preventDefault();
    CopyToClipboard(this);
    return false;
  });
  $(".backLink").click(function (event) {
    event.preventDefault();
    history.back(1);
  });
  $(".print").click(function () {
    let axx = $(this).attr("href");
    ClickPopup(axx);
    return false;
  });
  if ($(".noentry").length > 0) {
    let tagg = [];
    let content1 = $(".content1").html();
    let content2 = $(".content2").html();
    $(".onecms__tags a").each(function () {
      let tag = $(this).text().trim().replace("#", "");
      if (tag === undefined) return;
      tagg.push(tag);
    });
    if (tagg.length > 0) {
      try {
        for (i = 0; i < tagg.length; i++) {
          let regexExpression =
            "(?!(?:[^<]+>|[^>]+<\\/a>))\\b(" +
            change_title(tagg[i].trim()) +
            ")(?:s)?\\b";
          let regex = new RegExp(regexExpression, "imu");
          if (content1 !== undefined)
            content1 = content1.replace(
              regex,
              "<a href='/" +
              frienly_title(unescape(tagg[i])) +
              "-ptag.html" +
              "' title = '" +
              tagg[i] +
              "'>$1" +
              "</a>"
            );
          if (content2 !== undefined)
            content2 = content2.replace(
              regex,
              "<a href='/" +
              frienly_title(unescape(tagg[i])) +
              "-ptag.html" +
              "' title = '" +
              tagg[i] +
              "'>$1" +
              "</a>"
            );
        }
        if (content1 !== undefined) $(".content1").html(content1);
        if (content1 !== undefined) $(".content2").html(content2);
      } catch (e) {
        if (content1 !== undefined) $(".content1").html(content1);
        if (content1 !== undefined) $(".content2").html(content2);
        console.log(e.message);
      }
    }
  }
  $("a.comment").click(function () {
    $("html,body").animate(
      {
        scrollTop: $(".c-tags").offset().top - 20,
      },
      700
    );
    $(".txt-content").focus();
    $("textarea").css("border", "1px solid #11e666");
  });
  $(".btnEmail").click(function () {
    let email = $("#txt_email").val();
    if (email === undefined || $.trim(email) == "") {
      $(this)
        .parent()
        .addClass("has-error")
        .find("em")
        .html("Vui lòng nhập email của bạn!");
      return false;
    }

    if (!validateEmail(email)) {
      $(this)
        .parent()
        .addClass("has-error")
        .find("em")
        .html("Email của bạn không đúng định dạng!");
      return false;
    }
    if ($(this).parent().hasClass("has-error")) {
      $(this).parent().removeClass("has-error");
    }
    $(this).parent().find("em").html("");

    return false;

    if (true) {
      //$.ajax({
      //    url: "/api/registerEmail",
      //    type: "post",
      //    data: {
      //        email: email
      //    },
      //    success: function (data) {
      //        $(".c-loading").css("display", "none");
      //        if (data == "0") {
      //            alert("Email này của bạn đã được đăng ký trước đây!");
      //        } else
      //            alert("Đăng ký nhận bản tin thành công!");
      //        $('#txt_Email').val("");
      //    }
      //});
    }
  });
  refreshTimeAgo($(".b-grid__time"));

  //run paywall-popup

  if ($(".teaser").is(":visible")) {
    showPaywallMessagepremiumexpired();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  let loadMoreButton = document.getElementById("load_more");
  let isLoading = false;

  function showLoading() {
    const loadingImg = document.querySelector(".loading_img");
    if (loadingImg) loadingImg.style.display = "block";
  }

  function hideLoading() {
    const loadingImg = document.querySelector(".loading_img");
    if (loadingImg) loadingImg.style.display = "none";
  }

  function loadMoreArticles() {
    if (isLoading || !loadMoreButton) return;
    isLoading = true;
    showLoading();

    const link = loadMoreButton.querySelector("a");
    const channelId = link.dataset.channelId;
    const page = parseInt(link.dataset.page); // Ensure integer
    const limit = link.dataset.limit;

    fetch(`/loadmore/${channelId}/${page}/${limit}`)
      .then((response) => response.json())
      .then((data) => {
        hideLoading();
        isLoading = false;

        if (data?.ListArticleNewest?.length > 0) {
          const articleList = document.getElementById("article-list");
          const fragment = document.createDocumentFragment(); // Batch DOM updates

          data.ListArticleNewest.forEach((article) => {
            const articleItem = document.createElement("button");
            articleItem.className =
              "loadArticle md:py-4 py-3 border-b border-b-gray-400 w-full";
            articleItem.setAttribute("pid", article.PublisherId);
            articleItem.innerHTML = `
              <a href="${article.FriendlyTitle}-${article.PublisherId}.html">
                <div class="flex flex-row md:gap-x-12.5 gap-x-6 justify-between w-full">
                  <div class="flex flex-col md:flex-row md:gap-x-12.5 gap-y-3">
                    <p class="text-sm text-nowrap text-start" style="color: #B89659;">
                      ${new Date(
              article.Time_yyyyMMddHHmmss
            ).getDate()} Tháng ${new Date(article.Time_yyyyMMddHHmmss).getMonth() + 1
              }, ${new Date(article.Time_yyyyMMddHHmmss).getFullYear()}
                    </p>
                    <p class="font-bold md:text-lg text-base col-span-3 hover:underline text-start">
                      ${article.Title}
                    </p>
                                                                                        </div>
                  <div class="relative shrink-0 col-span-2 md:col-span-1 md:w-50 md:h-32.5 w-33 h-21 overflow-hidden">
                    <img src="${article.Thumbnail_540x360}" alt="${article.Title
              }" title="${article.Title}" class="w-full h-full" loading="lazy"/>
                                                                                    </div>
                                                                                </div>
              </a>
            `;
            fragment.appendChild(articleItem);
          });

          articleList.appendChild(fragment); // Single DOM insertion
          link.dataset.page = page + 1;
        } else {
          loadMoreButton.style.display = "none"; // Hide button when no more data
          window.removeEventListener("scroll", scrollHandler); // Remove scroll listener
        }
      })
      .catch((error) => {
        console.error("Error loading more articles:", error);
        hideLoading();
        isLoading = false;
        if (loadMoreButton) loadMoreButton.style.display = "none";
      });
  }

  // Debounced scroll handler
  function debounce(func, wait) {
    let timeout;
    return function (...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  }

  loadMoreArticles();

  loadMoreButton?.addEventListener("click", () => {
    loadMoreArticles();
  });
  // const scrollHandler = debounce(function () {
  //   if (!loadMoreButton) return;
  //   const buttonRect = loadMoreButton.getBoundingClientRect();
  //   const windowHeight = window.innerHeight;

  //   if (buttonRect.top < windowHeight && !isLoading) {
  //   }
  // }, 100);

  // window.addEventListener("scroll", scrollHandler);

  updateArtsCookie(); // Assuming this is defined elsewhere
});

function setCookie(name, value, days) {
  let expires = "";
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
  let nameEQ = name + "=";
  let ca = document.cookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) === " ") c = c.substring(1);
    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}

function updateArtsCookie() {
  const url = window.location.href;
  const publisherId = extractPublisherId(url);

  if (publisherId) {
    let arts = getCookie("bbw_arts");

    if (arts) {
      arts = JSON.parse(arts);
      if (arts.length !== 4) {
        if (!arts.includes(publisherId)) {
          arts.push(publisherId);
          setCookie("bbw_arts", JSON.stringify(arts), getDaysToEndOfMonth());
        }
      }
    } else {
      setCookie(
        "bbw_arts",
        JSON.stringify([publisherId]),
        getDaysToEndOfMonth()
      );
    }
  }
}

function getDaysToEndOfMonth() {
  const now = new Date();
  const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
  return Math.round((endOfMonth - now) / (1000 * 60 * 60 * 24));
}

function extractPublisherId(url) {
  const regex = /-(\d+)\.html$/;
  const match = url.match(regex);
  return match ? match[1] : null;
}

document.addEventListener("DOMContentLoaded", () => {
  fetch("/ads/all")
    .then((res) => res.json())
    .then((response) => {
      const ads = response.data;

      Object.entries(ads).forEach(([zoneId, htmlContent]) => {
        const container = document.getElementById(zoneId);
        if (!container) return;

        // Lấy tham chiếu đến skeleton-container
        const skeletonContainer = container.querySelector(
          ".skeleton-container"
        );

        // Nếu không có nội dung quảng cáo, giữ nguyên skeleton và không làm gì thêm
        if (!htmlContent) return;

        // Xóa placeholder hiện tại nếu có
        const placeholder = container.querySelector(".ad-placeholder");
        if (placeholder) placeholder.remove();

        // Thêm wrapper cho quảng cáo với z-index thấp hơn skeleton
        const adWrapper = document.createElement("div");
        adWrapper.classList.add(
          "absolute",
          "inset-0",
          "w-full",
          "h-full",
          "z-10"
        );
        adWrapper.style.opacity = "0"; // Bắt đầu với opacity=0
        adWrapper.innerHTML = htmlContent;
        container.appendChild(adWrapper);

        // Tìm tất cả media element trong quảng cáo
        const mediaElements = adWrapper.querySelectorAll("video, img, iframe");

        // Nếu có media element
        if (mediaElements.length > 0) {
          let loadedCount = 0;
          const totalMediaCount = mediaElements.length;

          // Xử lý cho từng media element
          mediaElements.forEach((media) => {
            // Thêm class cần thiết cho media
            media.classList.add("w-full", "h-full", "object-contain");

            // Xác định loại media và xử lý tương ứng
            if (media.tagName === "VIDEO") {
              // Kiểm tra nếu video đã load
              if (media.readyState >= 3) {
                // HAVE_FUTURE_DATA
                loadedCount++;
                checkAllLoaded();
              } else {
                // Thêm event listener cho video
                media.addEventListener("loadeddata", function () {
                  loadedCount++;
                  checkAllLoaded();
                });

                // Thêm timeout phòng hờ video không load được
                setTimeout(() => {
                  if (loadedCount < totalMediaCount) {
                    loadedCount = totalMediaCount;
                    checkAllLoaded();
                  }
                }, 3000);
              }
            } else {
              // Xử lý cho img và iframe
              if (media.complete) {
                loadedCount++;
                checkAllLoaded();
              } else {
                media.addEventListener("load", function () {
                  loadedCount++;
                  checkAllLoaded();
                });

                // Fallback nếu image không load được
                media.addEventListener("error", function () {
                  loadedCount++;
                  checkAllLoaded();
                });
              }
            }
          });

          // Kiểm tra xem tất cả media đã load xong chưa
          function checkAllLoaded() {
            if (loadedCount >= totalMediaCount) {
              showAd();
            }
          }
        } else {
          // Không có media, hiển thị quảng cáo sau một delay nhỏ
          setTimeout(showAd, 200);
        }

        // Hàm hiển thị quảng cáo
        function showAd() {
          // Hiển thị quảng cáo với transition mượt mà
          adWrapper.style.transition = "opacity 0.3s";
          adWrapper.style.opacity = "1";
          adWrapper.style.zIndex = "30"; // Đẩy lên trên skeleton

          // Ẩn skeleton
          if (skeletonContainer) {
            skeletonContainer.style.transition = "opacity 0.3s";
            skeletonContainer.style.opacity = "0";

            // Hoàn toàn ẩn skeleton sau khi animation kết thúc
            setTimeout(() => {
              skeletonContainer.style.display = "none";
            }, 300);
          }
        }
      });
    })
    .catch((err) => {
      console.error("Failed to load ads", err);
    });
});

document.getElementById("newsletter-btn").addEventListener("click", () => {
  const isHomepage = window.location.pathname === "/";
  const newsletterSectionId = "newsletter-section";
  const emailInputId = "mauticform_input_bbw_email";

  if (!isHomepage) {
    sessionStorage.setItem("scrollToNewsletter", "true");
    window.location.href = "/";
    return; // Exit early after setting redirect
  }

  scrollToNewsletter(newsletterSectionId, emailInputId);
});

function scrollToNewsletter(sectionId, inputId, isRedirect = false) {
  const newsletterSection = document.getElementById(sectionId);
  const emailInput = document.getElementById(inputId);

  if (!newsletterSection) return;

  // Wait for the next frame to ensure layout is stable
  requestAnimationFrame(() => {
    // Use a more reliable position calculation
    const targetPosition =
      newsletterSection.offsetTop -
      window.innerHeight / 2 +
      newsletterSection.offsetHeight / 2;

    // Add additional offset if this is after a redirect
    const finalPosition = isRedirect ? targetPosition + 100 : targetPosition;

    window.scrollTo({
      top: finalPosition,
      behavior: "smooth",
    });

    if (emailInput) {
      setTimeout(
        () => {
          try {
            emailInput.focus({ preventScroll: true });
          } catch (e) {
            // Fallback for browsers that don't support focus options
            emailInput.focus();
          }
        },
        isRedirect ? 1200 : 800
      );
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  if (sessionStorage.getItem("scrollToNewsletter") === "true") {
    // Add a small delay to ensure all layout is complete
    setTimeout(() => {
      scrollToNewsletter(
        "newsletter-section",
        "mauticform_input_bbw_email",
        true
      );
      sessionStorage.removeItem("scrollToNewsletter");
    }, 300); // Increased initial delay for mobile
  }
});


//gtm tracking article_view
document.addEventListener("DOMContentLoaded", () => {
  const gtmArticleData = document.getElementById('gtm-article-data');


  if (gtmArticleData) {
    const uuid = getCookie('bbw_uuid') || '';


    const dataLayerObject = {
      event: 'view_article',
      publisher_id: gtmArticleData.dataset.publisher,
      keyword: gtmArticleData.dataset.keyword,
      uuid: uuid
    };


    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(dataLayerObject);

    h
  } else {
    console.log('GTM Article Data Element not found');
  }
});