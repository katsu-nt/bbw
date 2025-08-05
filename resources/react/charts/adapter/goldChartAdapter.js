import { getDaysFromRange } from "@/lib/market/date";
import Highcharts from "highcharts";
const goldNameMap = {
  pnj: "PNJ",
  sjc: "SJC",
  xau_usd: "Vàng thế giới",
};

export const goldColors = [
  "#000000", "#0032F0", "#B51001", "#03eb03ff",
  "#fff240ff", "#008cffff", "#ff0360ff", "#480075ff", "#67879cff", "#0983caff",
];

export function goldSeriesAdapter({ items, data, range, mode, options, chartColors }) {
  const isNormalizeMode = mode === "normalize";
  const days = getDaysFromRange(range);

  let allDates = [];
  const series = items.map((item, idx) => {
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
      color: chartColors[idx % chartColors.length],
      yAxis: isNormalizeMode ? 0 : (item.gold_type === "xau_usd" && items.some(i => i.gold_type !== "xau_usd") ? 1 : 0),
      marker: { enabled: false, states: { hover: { enabled: true, radius: 4 } } },
      tooltip: {
        valueSuffix: isNormalizeMode ? "%" : (item.gold_type === "xau_usd" ? " USD" : " VND"),
        valueDecimals: 2,
      },
    };
  }).filter(Boolean);

  // Lấy span years (nếu cần)
  const sortedDates = [...new Set(allDates)].sort((a, b) => a - b);
  let spanYears = 0;
  if (sortedDates.length > 0) {
    const minDate = new Date(sortedDates[0]);
    const maxDate = new Date(sortedDates[sortedDates.length - 1]);
    spanYears = maxDate.getFullYear() - minDate.getFullYear();
  }

  return { series, allDates, spanYears };
}

export function goldYAxisAdapter({ items, mode }) {
  const isNormalizeMode = mode === "normalize";
  const hasDomestic = items.some(i => i.gold_type !== "xau_usd");
  const hasGlobal = items.some(i => i.gold_type === "xau_usd");
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
  return yAxis;
}

export function goldTooltipFormatter(that, mode) {
  const isNormalizeMode = mode === "normalize";
  let tooltip = `<div style="font-size:12px; font-weight:400;">`;
  tooltip += `<b>${Highcharts.dateFormat("%d/%m/%Y", that.x)}</b><br/>`;
  that.points.forEach((point) => {
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
}

export function goldLegendItem({
  item, idx, color, items, setItems, data, range, mode
}) {
  const days = getDaysFromRange(range);
  const key = `${item.gold_type}-${item.location}`;
  const goldData = data[key]?.[days] || [];
  const base = goldData[0]?.price;
  const last = goldData.at(-1)?.price;
  const change = base && last ? ((last - base) / base) * 100 : null;
  const name = goldNameMap[item.gold_type] || item.gold_type;
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
      <span className="text-sm text-black">{name}</span>
      <span className={isNormalizeMode ? "text-[#595959]" : "text-[#191919]"}>
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
      {items.length > 1 && (
        <button
          onClick={() =>
            setItems((prev) =>
              prev.filter(
                (x) =>
                  x.gold_type !== item.gold_type ||
                  x.location !== item.location
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

export function goldUnitSideText(items, mode) {
  if (mode === "normalize") return null;
  const hasDomestic = items.some(i => i.gold_type !== "xau_usd");
  const hasGlobal = items.some(i => i.gold_type === "xau_usd");
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
