import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";
import { MinusCircle } from "lucide-react";

const colors = [
  "#000000", "#0032F0", "#B51001", "#03eb03ff",
  "#fff240ff", "#008cffff", "#ff0360ff", "#480075ff", "#67879cff", "#0983caff",
];

const displayNameMap = {
  "USD-VND": "USD/VND",
  "EUR-VND": "EUR/VND",
  "JPY-VND": "JPY/VND",
  "CNY-VND": "CNY/VND",
  "USD-JPY": "USD/JPY",
  "USD-CNY": "USD/CNY",
};

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

export default function ExchangeChart({ data, selectedItems, setSelectedItems, range }) {
  const days = getDaysFromRange(range);
  const isComparisonMode = selectedItems.length > 1;

  const allDates = [];
  const series = selectedItems
    .map((item, index) => {
      const key = `${item.base}-${item.quote}`;
      const chartData = data[key]?.[days] || [];
      if (chartData.length === 0) return null;
      const baseRate = chartData[0].rate;
      const color = colors[index % colors.length];
      const seriesData = chartData.map((entry) => {
        const timestamp = new Date(entry.date).getTime();
        allDates.push(timestamp);
        return [
          timestamp,
          isComparisonMode
            ? ((entry.rate - baseRate) / baseRate) * 100
            : entry.rate,
        ];
      });
      return {
        name: displayNameMap[key] || key,
        data: seriesData,
        color,
        tooltip: {
          valueSuffix: isComparisonMode ? "%" : "",
          valueDecimals: isComparisonMode ? 2 : 2,
        },
        marker: { enabled: false, states: { hover: { enabled: true, radius: 4 } } },
      };
    })
    .filter(Boolean);

  const sortedDates = allDates.sort((a, b) => a - b);
  const minDate = sortedDates[0];
  const maxDate = sortedDates[sortedDates.length - 1];
  const spanYears = new Date(maxDate).getFullYear() - new Date(minDate).getFullYear();

  let yTickPositions;
  if (isComparisonMode) {
    const allY = series.flatMap((s) => s.data.map(([_, y]) => y));
    const yMin = Math.min(...allY);
    const yMax = Math.max(...allY);
    const absMax = Math.max(Math.abs(yMin), Math.abs(yMax));
    const rounded = Math.ceil(absMax / 2) * 2;
    yTickPositions = [-rounded, 0, rounded];
  }

  const options = {
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
        {selectedItems.map((item, index) => {
          const key = `${item.base}-${item.quote}`;
          const color = colors[index % colors.length];
          const chartData = data[key]?.[days] || [];
          const baseRate = chartData[0]?.rate;
          const lastRate = chartData.at(-1)?.rate;
          const change =
            baseRate && lastRate
              ? ((lastRate - baseRate) / baseRate) * 100
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
                {displayNameMap[key] || key}
              </span>
              <span
                className={isComparisonMode ? "text-[#595959]" : "text-black"}
              >
                {change !== null
                  ? isComparisonMode
                    ? change >= 0
                      ? `+${change.toFixed(2)}%`
                      : `${change.toFixed(2)}%`
                    : lastRate?.toLocaleString("vi-VN", { maximumFractionDigits: 2 })
                  : "-"}
              </span>

              {isComparisonMode && (
                <button
                  onClick={() =>
                    setSelectedItems((prev) =>
                      prev.filter(
                        (x) =>
                          x.base !== item.base ||
                          x.quote !== item.quote
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
        <div className="text-xs text-[#595959] mb-4">Đơn vị: ngàn đồng</div>
      )}
      <HighchartsReact highcharts={Highcharts} options={options} />
    </div>
  );
}
