import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";
import { MinusCircle } from "lucide-react";

const colors = [
  "#000000", "#0032F0", "#B51001", "#03eb03ff",
  "#fff240ff", "#008cffff", "#ff0360ff", "#480075ff", "#67879cff", "#0983caff",
];

const goldNameMap = {
  pnj: "PNJ",
  sjc: "SJC",
  xau_usd: "Vàng thế giới",
};

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

export default function GoldChart({
  mode,
  goldItems,
  setGoldItems,
  data,
  range,
  options = [],
}) {
  const isNormalizeMode = mode === "normalize";

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
  const days = getDaysFromRange(range);

  const hasDomestic = goldItems.some(i => i.gold_type !== "xau_usd");
  const hasGlobal = goldItems.some(i => i.gold_type === "xau_usd");

  let allDates = [];
  const series = goldItems.map((item, idx) => {
    const key = `${item.gold_type}-${item.location}`;
    const goldData = data[key]?.[days] || [];
    if (goldData.length === 0) return null;
    const basePrice = goldData[0].price;
    const seriesData = goldData.map((entry) => {
      const timestamp = new Date(entry.date).getTime();
      allDates.push(timestamp);
      return [
        timestamp,
        isNormalizeMode
          ? ((entry.price - basePrice) / basePrice) * 100
          : entry.price
      ];
    });
    const name = goldNameMap[item.gold_type] || item.gold_type;
    return {
      name,
      data: seriesData,
      color: colors[idx % colors.length],
      yAxis: isNormalizeMode ? 0 : (item.gold_type === "xau_usd" && hasDomestic ? 1 : 0),
      marker: { enabled: false, states: { hover: { enabled: true, radius: 4 } } },
      tooltip: {
        valueSuffix: isNormalizeMode ? "%" : (item.gold_type === "xau_usd" ? " USD" : " VND"),
        valueDecimals: 2,
      },
    };
  }).filter(Boolean);

  // Tính các mốc thời gian X chia đều
  const sortedDates = [...new Set(allDates)].sort((a, b) => a - b);
  let spanYears = 0;
  let xTickPositions = [];
  if (sortedDates.length > 0) {
    const minDate = new Date(sortedDates[0]);
    const maxDate = new Date(sortedDates[sortedDates.length - 1]);
    spanYears = maxDate.getFullYear() - minDate.getFullYear();
    xTickPositions = getDateRangeTicks(minDate, maxDate, 5);
  }

  // Y Axis logic như trước
  let yAxis = [];
  if (isNormalizeMode) {
    yAxis = [{
      title: { text: null },
      labels: { formatter() { return Math.round(this.value) + "%"; } },
      tickAmount: 4,
      opposite: true,
    }];
  } else {
    if (hasDomestic && hasGlobal) {
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() { return parseInt(this.value).toLocaleString("vi-VN"); }
        },
        tickAmount: 4,
        opposite: false,
      });
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() { return parseInt(this.value).toLocaleString("en-US"); }
        },
        tickAmount: 4,
        opposite: true,
      });
    } else if (hasGlobal && !hasDomestic) {
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() { return parseInt(this.value).toLocaleString("en-US"); }
        },
        tickAmount: 4,
        opposite: false,
      });
    } else {
      yAxis.push({
        title: { text: null },
        labels: {
          formatter() { return parseInt(this.value).toLocaleString("vi-VN"); }
        },
        tickAmount: 4,
        opposite: false,
      });
    }
  }

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
          // Check là vàng nội địa hay vàng thế giới
          const isVangNoiDia = point.series.userOptions.yAxis !== 1;
          const value = isNormalizeMode
            ? (point.y >= 0 ? "+" : "") + point.y.toFixed(2) + "%"
            : isVangNoiDia
            ? (point.y * 1000).toLocaleString("vi-VN", { maximumFractionDigits: 0 }) + " VND"
            : point.y.toLocaleString("en-US", { maximumFractionDigits: 2 }) + " USD";
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
    if (hasDomestic && hasGlobal) {
      return (
        <div className="flex justify-between">
          <span>Đơn vị: ngàn đồng/lượng</span>
          <span>Đơn vị: USD/oz</span>
        </div>
      );
    }
    if (hasGlobal) return "Đơn vị: USD/oz";
    if (hasDomestic) return "Đơn vị: ngàn đồng/lượng";
    return null;
  }

  return (
    <div className="w-full min-h-[440px]">
      <div className="flex flex-wrap gap-4 items-center mb-4">
        {goldItems.map((item, idx) => {
          const key = `${item.gold_type}-${item.location}`;
          const color = colors[idx % colors.length];
          const goldData = data[key]?.[days] || [];
          const base = goldData[0]?.price;
          const last = goldData.at(-1)?.price;
          const change =
            base && last ? ((last - base) / base) * 100 : null;
          const name = goldNameMap[item.gold_type] || item.gold_type;
          return (
            <div
              key={key}
              className="flex items-center gap-2 px-3 py-1 text-sm font-normal bg-[#F7F7F7] rounded-md"
            >
              <span
                className="inline-block w-2 h-2 rounded-full"
                style={{ backgroundColor: color }}
              ></span>
              <span className="text-sm text-black">{name}</span>
              <span className={isNormalizeMode ? "text-[#595959]" : "text-black"}>
                {isNormalizeMode
                  ? change !== null
                    ? change >= 0
                      ? `+${change.toFixed(2)}%`
                      : `${change.toFixed(2)}%`
                    : "-"
                  : item.gold_type === "xau_usd"
                  ? last
                    ? last.toLocaleString("en-US", { maximumFractionDigits: 2 }) + " USD"
                    : "-"
                  : last
                  ? (last * 1000).toLocaleString("vi-VN") + " VND"
                  : "-"}
              </span>
              {goldItems.length > 1 && (
                <button
                  onClick={() =>
                    setGoldItems((prev) =>
                      prev.filter(
                        (x) =>
                          x.gold_type !== item.gold_type ||
                          x.location !== item.location
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
