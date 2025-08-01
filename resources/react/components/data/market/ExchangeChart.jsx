import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";
import { MinusCircle } from "lucide-react";

// Danh sách màu cho các line
const colors = [
  "#000000", "#0032F0", "#B51001", "#03eb03ff",
  "#fff240ff", "#008cffff", "#ff0360ff", "#480075ff", "#67879cff", "#0983caff",
];

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
  selected,
  setSelected,
  compareItems,
  setCompareItems,
  data,
  range,
  options = [],
}) {
  const days = getDaysFromRange(range);
  const isComparisonMode = mode === "compare";

  // Tạo mảng chartItems dạng [{code, type}]
  let chartItems = [];
  if (!isComparisonMode) {
    chartItems = selected && selected.code ? [selected] : [{ code: "USD", type: "market" }];
  } else {
    chartItems = compareItems;
  }

  // Helper: Tìm tên hiển thị
  function getDisplayName(item) {
    const found = options.find(
      (o) => o.code === item.code && o.type === item.type
    );
    if (found) return found.name;
    if (item.type && item.code) {
      let typeLabel = "";
      if (item.type === "market") typeLabel = "";
      else if (item.type === "central") typeLabel = " (SBVN)";
      else if (item.type === "index") typeLabel = "";
      return `${item.code}${typeLabel}`;
    }
    return item.code || "";
  }

  // Build chart data
  const allDates = [];
  const series = chartItems
    .map((item, index) => {
      const key = `${item.type}-${item.code}`;
      const chartData = data[key]?.[days] || [];
      if (chartData.length === 0) return null;
      // Dữ liệu: market/central dùng 'rate', index dùng 'value'
      const getVal = (row) => row.rate !== undefined ? row.rate : row.value;
      const baseVal = getVal(chartData[0]);
      const color = colors[index % colors.length];
      const seriesData = chartData.map((entry) => {
        const timestamp = new Date(entry.date || entry.timestamp).getTime();
        allDates.push(timestamp);
        return [
          timestamp,
          isComparisonMode
            ? ((getVal(entry) - baseVal) / baseVal) * 100
            : getVal(entry),
        ];
      });
      return {
        name: getDisplayName(item),
        data: seriesData,
        color,
        tooltip: {
          valueSuffix: isComparisonMode ? "%" : "",
          valueDecimals: 2,
        },
        marker: { enabled: false, states: { hover: { enabled: true, radius: 4 } } },
      };
    })
    .filter(Boolean);

  const sortedDates = allDates.sort((a, b) => a - b);
  const minDate = sortedDates[0];
  const maxDate = sortedDates[sortedDates.length - 1];
  const spanYears = minDate && maxDate
    ? new Date(maxDate).getFullYear() - new Date(minDate).getFullYear()
    : 0;

  let yTickPositions;
  if (isComparisonMode && series.length > 0) {
    const allY = series.flatMap((s) => s.data.map(([_, y]) => y));
    const yMin = Math.min(...allY);
    const yMax = Math.max(...allY);
    const diff = yMax - yMin;
    let roundedMin, roundedMax;
    if (diff <= 10) {
      roundedMin = Math.floor(yMin);
      roundedMax = Math.ceil(yMax);
    } else {
      roundedMin = Math.floor(yMin / 3) * 3;
      roundedMax = Math.ceil(yMax);
    }
    yTickPositions = [roundedMin, 0, roundedMax];
  }

  const optionsChart = {
    chart: { type: "line", zoomType: "x", height: 400 },
    title: { text: null },
    xAxis: {
      type: "datetime",
      title: { text: null },
      crosshair: { width: 1, color: "#ccc", dashStyle: "ShortDot" },
      min: spanYears < 4 ? minDate : undefined,
      max: spanYears < 4 ? maxDate : undefined,
      tickPositions: (() => {
        const uniqueDates = [...new Set(sortedDates)];
        const total = uniqueDates.length;
        if (total <= 5) return uniqueDates;
        const step = Math.floor(total / 4);
        const ticks = [uniqueDates[0]];
        for (let i = 1; i < 4; i++) {
          ticks.push(uniqueDates[i * step]);
        }
        ticks.push(uniqueDates[total - 1]);
        return ticks;
      })(),
      labels: {
        formatter: function () {
          const date = new Date(this.value);
          return spanYears >= 4
            ? date.getFullYear()
            : `${String(date.getDate()).padStart(2, "0")}/${String(
                date.getMonth() + 1
              ).padStart(2, "0")}`;
        },
      },
    },
    yAxis: {
      opposite: isComparisonMode,
      title: { text: null },
      labels: {
        formatter: function () {
          return isComparisonMode
            ? `${this.value.toFixed(2)}%`
            : this.value.toLocaleString("vi-VN", { maximumFractionDigits: 2 });
        },
      },
      tickPositions: (() => {
        if (isComparisonMode) return yTickPositions;
        const allY = series.flatMap((s) => s.data.map(([_, y]) => y));
        if (allY.length === 0) return undefined;
        const yMin = Math.min(...allY);
        const yMax = Math.max(...allY);
        const range = yMax - yMin;
        if (range === 0) {
          const rounded = Math.round(yMin / 100) * 100;
          return [rounded - 100, rounded, rounded + 100];
        }
        const step = Math.ceil(range / 4 / 100) * 100;
        const start = Math.floor(yMin / 100) * 100;
        return Array.from({ length: 5 }, (_, i) => start + i * step);
      })(),
    },
    tooltip: {
      shared: true,
      xDateFormat: "%d/%m/%Y",
      formatter: function () {
        const isComparison = isComparisonMode;
        let tooltip = `<div style="font-size:12px; font-weight:400;">`;
        tooltip += `<b>${Highcharts.dateFormat("%d/%m/%Y", this.x)}</b><br/>`;
        this.points.forEach((point) => {
          const value = isComparison
            ? (point.y >= 0 ? "+" : "") + point.y.toFixed(2) + "%"
            : point.y.toLocaleString("vi-VN", { maximumFractionDigits: 2 });
          const valueColor = isComparison ? "#595959" : "#000000";
          tooltip += `
            <br/>
            <span style="color:${point.color}">●</span>
            <b> ${point.series.name}: </b>
            <b style="color:${valueColor}">${value}</b><br/>
          `;
        });
        tooltip += `</div>`;
        return tooltip;
      },
    },
    legend: { enabled: false },
    credits: { enabled: false },
    series: series.map((s) => ({
      ...s,
      marker: { enabled: false },
    })),
  };

  return (
    <div className="w-full min-h-[440px]">
      <div className="flex flex-wrap gap-4 items-center mb-4">
        {chartItems.map((item, index) => {
          const key = `${item.type}-${item.code}`;
          const color = colors[index % colors.length];
          const chartData = data[key]?.[days] || [];
          const getVal = (row) => row.rate !== undefined ? row.rate : row.value;
          const baseVal = chartData[0] ? getVal(chartData[0]) : null;
          const lastVal = chartData.at(-1) ? getVal(chartData.at(-1)) : null;
          const change =
            baseVal && lastVal
              ? ((lastVal - baseVal) / baseVal) * 100
              : null;

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
                {getDisplayName(item)}
              </span>
              <span className={isComparisonMode ? "text-[#595959]" : "text-black"}>
                {change !== null
                  ? isComparisonMode
                    ? change >= 0
                      ? `+${change.toFixed(2)}%`
                      : `${change.toFixed(2)}%`
                    : lastVal?.toLocaleString("vi-VN", { maximumFractionDigits: 2 })
                  : "-"}
              </span>
              {isComparisonMode && chartItems.length > 1 && (
                <button
                  onClick={() =>
                    setCompareItems((prev) =>
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
      {!isComparisonMode && (
        <div className="text-xs text-[#595959] mb-4">
          {chartItems[0]?.type === "index"
            ? "Đơn vị: điểm"
            : "Đơn vị: ngàn đồng"}
        </div>
      )}
      <HighchartsReact highcharts={Highcharts} options={optionsChart} />
    </div>
  );
}
