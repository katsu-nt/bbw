import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";
import { MinusCircle } from "lucide-react";

const colors = [
  "#000000", "#0032F0", "#B51001", "#03eb03ff",
  "#fff240ff", "#008cffff", "#ff0360ff", "#480075ff", "#67879cff", "#0983caff",
];

// Hàm tạo dải ngày đều cho tick X
function getDateRangeTicks(startDate, endDate, maxTicks = 5) {
  const ticks = [];
  const dayMs = 24 * 3600 * 1000;
  const totalDays = Math.floor((endDate - startDate) / dayMs);
  if (totalDays <= maxTicks - 1) {
    for (let i = 0; i <= totalDays; ++i) {
      ticks.push(startDate.getTime() + i * dayMs);
    }
  } else {
    for (let i = 0; i < maxTicks; ++i) {
      const t = Math.round(i * totalDays / (maxTicks - 1));
      ticks.push(startDate.getTime() + t * dayMs);
    }
  }
  return ticks;
}

function getDaysFromRange(range) {
  const today = new Date();
  switch (range) {
    case "7d": return 7;
    case "30d": return 30;
    case "6m": return 180;
    case "1y": return 365;
    case "5y": return 1825;
    case "ytd": {
      const start = new Date(today.getFullYear(), 0, 1);
      return Math.floor((today - start) / (1000 * 60 * 60 * 24)) + 1;
    }
    default: return 30;
  }
}

export default function ExchangeChart({
  mode,
  chartItems,
  setChartItems,
  data,
  range,
  options = [],
}) {
  const days = getDaysFromRange(range);
  const isNormalizeMode = mode === "normalize";

  const hasMarket = chartItems.some(i => i.type === "market" || i.type === "central");
  const hasIndex = chartItems.some(i => i.type === "index");

  let allDates = [];
  const series = chartItems.map((item, idx) => {
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
      options.find(
        (o) => o.code === item.code && o.type === item.type
      )?.name || item.code;
    return {
      name: displayName,
      data: seriesData,
      color: colors[idx % colors.length],
      yAxis: isNormalizeMode ? 0 : (item.type === "index" && hasMarket ? 1 : 0),
      marker: { enabled: false, states: { hover: { enabled: true, radius: 4 } } },
      tooltip: {
        valueSuffix: isNormalizeMode ? "%" : (item.type === "index" ? " điểm" : " ngàn đồng"),
        valueDecimals: 2,
      },
    };
  }).filter(Boolean);

  // Tick X chia đều: luôn max 5 mốc, có ngày đầu/cuối
  const sortedDates = [...new Set(allDates)].sort((a, b) => a - b);
  let spanYears = 0;
  let xTickPositions = [];
  if (sortedDates.length > 0) {
    const minDate = new Date(sortedDates[0]);
    const maxDate = new Date(sortedDates[sortedDates.length - 1]);
    spanYears = maxDate.getFullYear() - minDate.getFullYear();
    xTickPositions = getDateRangeTicks(minDate, maxDate, 5);
  }

  // Build yAxis như cũ
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
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() {
            return parseInt(this.value).toLocaleString("vi-VN");
          }
        },
        tickAmount: 4,
        opposite: false,
      });
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() {
            return parseInt(this.value).toLocaleString("vi-VN");
          }
        },
        tickAmount: 4,
        opposite: true,
      });
    } else if (hasIndex && !hasMarket) {
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() {
            return parseInt(this.value).toLocaleString("vi-VN");
          }
        },
        tickAmount: 4,
        opposite: false,
      });
    } else {
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() {
            return parseInt(this.value).toLocaleString("vi-VN");
          }
        },
        tickAmount: 4,
        opposite: false,
      });
    }
  }

  // Tooltip
  const optionsChart = {
    chart: { type: "line", zoomType: "x", height: 400 },
    title: { text: null },
    xAxis: {
      type: "datetime",
      title: { text: null },
      crosshair: { width: 1, color: "#ccc", dashStyle: "ShortDot" },
      tickPositions: xTickPositions,
      labels: {
        formatter: function () {
          const date = new Date(this.value);
          return spanYears >= 3
            ? date.getFullYear()
            : `${String(date.getDate()).padStart(2, "0")}/${String(
                date.getMonth() + 1
              ).padStart(2, "0")}`;
        },
      },
    },
    yAxis: yAxis,
    tooltip: {
      shared: true,
      xDateFormat: "%d/%m/%Y",
      formatter: function () {
        let tooltip = `<div style="font-size:12px; font-weight:400;">`;
        tooltip += `<b>${Highcharts.dateFormat("%d/%m/%Y", this.x)}</b><br/>`;
        this.points.forEach((point) => {
          const name = point.series.name;
          const value = isNormalizeMode
            ? (point.y >= 0 ? "+" : "") + point.y.toFixed(2) + "%"
            : point.y.toLocaleString("vi-VN", { maximumFractionDigits: 2 }) +
              (point.series.userOptions.yAxis === 1 ? " điểm" : " ngàn đồng");
          tooltip += `
            <br/>
            <span style="color:${point.color}">●</span>
            <b> ${name}: </b>
            <b>${value}</b><br/>
          `;
        });
        tooltip += `</div>`;
        return tooltip;
      },
    },
    legend: { enabled: false },
    credits: { enabled: false },
    series: series,
  };

  function getUnitSideText() {
    if (isNormalizeMode) return null;
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

  return (
    <div className="w-full min-h-[440px]">
      <div className="flex flex-wrap gap-4 items-center mb-4">
        {chartItems.map((item, idx) => {
          const key = `${item.type}-${item.code}`;
          const color = colors[idx % colors.length];
          const chartData = data[key]?.[days] || [];
          const getVal = (row) => row.rate !== undefined ? row.rate : row.value;
          const baseVal = chartData[0] ? getVal(chartData[0]) : null;
          const lastVal = chartData.at(-1) ? getVal(chartData.at(-1)) : null;
          const change =
            baseVal && lastVal
              ? ((lastVal - baseVal) / baseVal) * 100
              : null;
          const displayName =
            options.find(
              (o) => o.code === item.code && o.type === item.type
            )?.name || item.code;
          return (
            <div
              key={key}
              className="flex items-center gap-2 px-3 py-1 text-sm font-normal bg-[#F7F7F7] rounded-md"
            >
              <span
                className="inline-block w-2 h-2 rounded-full"
                style={{ backgroundColor: color }}
              ></span>
              <span className="text-sm text-black">
                {displayName}
              </span>
              <span className={isNormalizeMode ? "text-[#595959]" : "text-black"}>
                {isNormalizeMode
                  ? change !== null
                    ? change >= 0
                      ? `+${change.toFixed(2)}%`
                      : `${change.toFixed(2)}%`
                    : "-"
                  : lastVal?.toLocaleString("vi-VN", { maximumFractionDigits: 2 }) ||
                    "-"}
              </span>
              {chartItems.length > 1 && (
                <button
                  onClick={() =>
                    setChartItems((prev) =>
                      prev.filter(
                        (x) =>
                          x.code !== item.code ||
                          x.type !== item.type
                      )
                    )
                  }
                >
                  <MinusCircle className="w-4 h-4 ml-1 opacity-70 hover:opacity-100" />
                </button>
              )}
            </div>
          );
        })}
      </div>
      {getUnitSideText() && (
        <div className="text-xs text-[#595959] mb-4">{getUnitSideText()}</div>
      )}
      <HighchartsReact highcharts={Highcharts} options={optionsChart} />
    </div>
  );
}
