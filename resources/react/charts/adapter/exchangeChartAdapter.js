import Highcharts from "highcharts";
import { getDaysFromRange } from "@/lib/market/date";

// Định nghĩa màu
export const exchangeColors = [
  "#000000", "#0032F0", "#B51001", "#03eb03ff",
  "#fff240ff", "#008cffff", "#ff0360ff", "#480075ff", "#67879cff", "#0983caff",
];

// Map code thành tên đẹp hơn nếu cần
const exchangeNameMap = {
  USD: "USD",
  DXY: "DXY",
  // ... thêm nếu có
};

// Tạo series
export function exchangeSeriesAdapter({ items, data, range, mode, options, chartColors }) {
  const isNormalizeMode = mode === "normalize";
  const days = getDaysFromRange(range);

  // Check có loại "market/central" và loại "index"
  const hasMarket = items.some(i => i.type === "market" || i.type === "central");
  const hasIndex = items.some(i => i.type === "index");

  let allDates = [];
  const series = items.map((item, idx) => {
    const key = `${item.type}-${item.code}`;
    const chartData = data[key]?.[days] || [];
    if (chartData.length === 0) return null;
    const getVal = (row) => row.rate !== undefined ? row.rate : row.value;
    const baseVal = getVal(chartData[0]);
    const seriesData = chartData.map((entry) => {
      const timestamp = new Date(entry.date || entry.timestamp).getTime();
      allDates.push(timestamp);
      return [
        timestamp,
        isNormalizeMode
          ? ((getVal(entry) - baseVal) / baseVal) * 100
          : getVal(entry)
      ];
    });
    const displayName =
      options?.find(
        (o) => o.code === item.code && o.type === item.type
      )?.name || exchangeNameMap[item.code] || item.code;
    // Xác định đúng yAxis cho từng loại
    let yAxisNum = 0;
    if (!isNormalizeMode && hasMarket && hasIndex) {
      yAxisNum = (item.type === "index") ? 1 : 0;
    }
    return {
      name: displayName,
      data: seriesData,
      color: chartColors[idx % chartColors.length],
      yAxis: isNormalizeMode ? 0 : yAxisNum,
      marker: { enabled: false, states: { hover: { enabled: true, radius: 4 } } },
      tooltip: {
        valueSuffix: isNormalizeMode ? "%" : (item.type === "index" ? " điểm" : " ngàn đồng"),
        valueDecimals: 2,
      },
    };
  }).filter(Boolean);

  // spanYears (cho tick xAxis)
  const sortedDates = [...new Set(allDates)].sort((a, b) => a - b);
  let spanYears = 0;
  if (sortedDates.length > 0) {
    const minDate = new Date(sortedDates[0]);
    const maxDate = new Date(sortedDates[sortedDates.length - 1]);
    spanYears = maxDate.getFullYear() - minDate.getFullYear();
  }
  return { series, allDates, spanYears };
}

// YAxis
export function exchangeYAxisAdapter({ items, mode }) {
  const isNormalizeMode = mode === "normalize";
  const hasMarket = items.some(i => i.type === "market" || i.type === "central");
  const hasIndex = items.some(i => i.type === "index");
  let yAxis = [];

  if (isNormalizeMode) {
    yAxis = [{
      title: { text: null },
      labels: { formatter() { return Math.round(this.value) + "%"; } },
      tickAmount: 4,
      opposite: true,
    }];
  } else {
    if (hasMarket && hasIndex) {
      // Luôn trả về 2 trục nếu có cả market và index
      yAxis = [
        {
          title: { text: null },
          labels: {
            formatter() { return parseInt(this.value).toLocaleString("vi-VN"); }
          },
          tickAmount: 4,
          opposite: false,
        },
        {
          title: { text: null },
          labels: {
            formatter() { return parseInt(this.value).toLocaleString("vi-VN"); }
          },
          tickAmount: 4,
          opposite: true,
        }
      ];
    } else {
      yAxis = [{
        title: { text: null },
        labels: {
          formatter() { return parseInt(this.value).toLocaleString("vi-VN"); }
        },
        tickAmount: 4,
        opposite: false,
      }];
    }
  }
  return yAxis;
}

// Tooltip
export function exchangeTooltipFormatter(that, mode) {
  const isNormalizeMode = mode === "normalize";
  let tooltip = `<div style="font-size:12px; font-weight:400;">`;
  tooltip += `<b>${Highcharts.dateFormat("%d/%m/%Y", that.x)}</b><br/>`;
  that.points.forEach((point) => {
    const name = point.series.name;
    const value = isNormalizeMode
      ? (point.y >= 0 ? "+" : "") + point.y.toFixed(2) + "%"
      : point.series.userOptions.yAxis === 1
      ? point.y.toLocaleString("vi-VN", { maximumFractionDigits: 2 }) + " điểm"
      : point.y.toLocaleString("vi-VN", { maximumFractionDigits: 2 }) + " ngàn đồng";
    tooltip += `
      <br/>
      <span style="color:${point.color}">●</span>
      <b> ${name}: </b>
      <b>${value}</b><br/>
    `;
  });
  tooltip += `</div>`;
  return tooltip;
}

// Legend giống vàng
export function exchangeLegendItem({
  item, idx, color, items, setItems, data, range, mode, options
}) {
  const days = getDaysFromRange(range);
  const key = `${item.type}-${item.code}`;
  const chartData = data[key]?.[days] || [];
  const getVal = (row) => row.rate !== undefined ? row.rate : row.value;
  const baseVal = chartData[0] ? getVal(chartData[0]) : null;
  const lastVal = chartData.at(-1) ? getVal(chartData.at(-1)) : null;
  const change =
    baseVal && lastVal
      ? ((lastVal - baseVal) / baseVal) * 100
      : null;
  const displayName =
    options?.find(
      (o) => o.code === item.code && o.type === item.type
    )?.name || item.code;
  const isNormalizeMode = mode === "normalize";
  return (
    <div
      key={key}
      className="flex items-center gap-2 px-3 py-1 text-sm font-normal bg-[#F7F7F7] rounded-md"
    >
      <span
        className="inline-block w-2 h-2 rounded-full"
        style={{ backgroundColor: color }}
      ></span>
      <span className="text-sm text-black">{displayName}</span>
      <span className={isNormalizeMode ? "text-[#595959]" : "text-[#191919]"}>
        {isNormalizeMode
          ? change !== null
            ? change >= 0
              ? `+${change.toFixed(2)}%`
              : `${change.toFixed(2)}%`
            : "-"
          : lastVal?.toLocaleString("vi-VN", { maximumFractionDigits: 2 }) ||
            "-"}
      </span>
      {items.length > 1 && (
        <button
          onClick={() =>
            setItems((prev) =>
              prev.filter(
                (x) =>
                  x.code !== item.code ||
                  x.type !== item.type
              )
            )
          }
        >
          <svg width="16" height="16" className="ml-1"><circle cx="8" cy="8" r="7" stroke="#191919" fill="none" /><line x1="5" y1="8" x2="11" y2="8" stroke="#191919" strokeWidth="2" /></svg>
        </button>
      )}
    </div>
  );
}

// Đơn vị
export function exchangeUnitSideText(items, mode) {
  if (mode === "normalize") return null;
  const hasMarket = items.some(i => i.type === "market" || i.type === "central");
  const hasIndex = items.some(i => i.type === "index");
  if (hasMarket && hasIndex) {
    return (
      <div className="flex justify-between">
        <span>Đơn vị: ngàn đồng</span>
        <span>Đơn vị: điểm</span>
      </div>
    );
  }
  if (hasIndex) return "Đơn vị: điểm";
  if (hasMarket) return "Đơn vị: ngàn đồng";
  return null;
}
