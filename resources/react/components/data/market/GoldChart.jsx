import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";
import { MinusCircle } from "lucide-react";

const colors = [
  "#000000", 
  "#0032F0", 
  "#B51001", 
  "#03eb03ff",
  "#fff240ff", 
  "#008cffff", 
  "#ff0360ff", 
  "#480075ff", 
  "#67879cff", 
  "#0983caff", 
];

const displayNameMap = {
  pnj: "PNJ",
  sjc: "SJC",
  nhẫn_trơn_pnj_9999: "Nhẫn Trơn PNJ 999.9",
  vàng_kim_bảo_9999: "Vàng Kim Bảo 999.9",
  vàng_phúc_lộc_tài_9999: "Vàng Phúc Lộc Tài 999.9",
  vàng_nữ_trang_9999: "Nữ Trang 999.9",
  vàng_nữ_trang_999: "Nữ Trang 999",
  vàng_nữ_trang_9920: "Nữ Trang 9920",
  vàng_nữ_trang_99: "Nữ Trang 99",
  vàng_916_22k: "Vàng 916 (22K)",
  vàng_750_18k: "Vàng 750 (18K)",
  vàng_680_163k: "Vàng 680 (16.3K)",
  vàng_650_156k: "Vàng 650 (15.6K)",
  vàng_610_146k: "Vàng 610 (14.6K)",
  vàng_585_14k: "Vàng 585 (14K)",
  vàng_416_10k: "Vàng 416 (10K)",
  vàng_375_9k: "Vàng 375 (9K)",
  vàng_333_8k: "Vàng 333 (8K)",
};

const formatPrice = (value) => {
  if (value) {
    value *= 1000;
  }
  return value?.toLocaleString("vi-VN", { maximumFractionDigits: 0 });
};

const formatPercent = (value) => `${value.toFixed(2)}%`;

const getDaysFromRange = (range) => {
  switch (range) {
    case "7d":
      return 7;
    case "30d":
      return 30;
    case "6m":
      return 180;
    case "1y":
      return 365;
    case "5y":
      return 1825;
    case "ytd": {
      const now = new Date();
      const start = new Date(now.getFullYear(), 0, 1);
      return Math.floor((now - start) / (1000 * 60 * 60 * 24)) + 1;
    }
    default:
      return 30;
  }
};

export default function GoldChart({
  data,
  selectedItems,
  setSelectedItems,
  range,
}) {
  const days = getDaysFromRange(range);
  const isComparisonMode = selectedItems.length > 1;

  const allDates = [];

  const series = selectedItems
    .map((item, index) => {
      const key = `${item.gold_type}-${item.location}`;
      const goldData = data[key]?.[days] || [];
      if (goldData.length === 0) return null;

      const basePrice = goldData[0].price;
      const color = colors[index % colors.length];

      const seriesData = goldData.map((entry) => {
        const timestamp = new Date(entry.date).getTime();
        allDates.push(timestamp);
        return [
          timestamp,
          isComparisonMode
            ? ((entry.price - basePrice) / basePrice) * 100
            : entry.price,
        ];
      });

      return {
        name: displayNameMap[item.gold_type] || item.gold_type,
        data: seriesData,
        color,
        tooltip: {
          valueSuffix: isComparisonMode ? "%" : "",
          valueDecimals: isComparisonMode ? 1 : 0,
        },
        marker: {
          enabled: false,
          states: {
            hover: {
              enabled: true,
              radius: 4,
            },
          },
        },
      };
    })
    .filter(Boolean);

  const sortedDates = allDates.sort((a, b) => a - b);
  const minDate = sortedDates[0];
  const maxDate = sortedDates[sortedDates.length - 1];
  const spanYears =
    new Date(maxDate).getFullYear() - new Date(minDate).getFullYear();

  let yTickPositions;
  if (isComparisonMode) {
    const allY = series.flatMap((s) => s.data.map(([_, y]) => y));
    const yMin = Math.min(...allY);
    const yMax = Math.max(...allY);
    const absMax = Math.max(Math.abs(yMin), Math.abs(yMax));
    const rounded = Math.ceil(absMax / 10) * 10;
    yTickPositions = [-rounded, 0, rounded];
  }

  const options = {
    chart: {
      type: "line",
      zoomType: "x",
      height: 400,
    },
    title: { text: null },
    xAxis: {
      type: "datetime",
      title: { text: null },
      crosshair: {
        width: 1,
        color: "#ccc",
        dashStyle: "ShortDot",
      },
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
            ? `${this.value.toFixed(0)}%`
            : this.value.toLocaleString("vi-VN");
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
            : (point.y * 1000).toLocaleString("vi-VN");

          const valueColor = isComparison ? "#595959" : "#000000";

          tooltip += `
            <br/>
            <span style="color:${point.color}">●</span>
            <b> ${point.series.name}: </b>
            <b style="color:${valueColor}">${value}${
            isComparison ? "" : " ₫"
          }</b><br/>
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
          const key = `${item.gold_type}-${item.location}`;
          const color = colors[index % colors.length];
          const goldData = data[key]?.[days] || [];
          const basePrice = goldData[0]?.price;
          const lastPrice = goldData.at(-1)?.price;
          const change =
            basePrice && lastPrice
              ? ((lastPrice - basePrice) / basePrice) * 100
              : null;

          return (
            <div
              key={key}
              className="flex items-center gap-2 px-3 py-1 text-sm font-normal bg-[#F7F7F7]"
            >
              <span
                className="inline-block w-2 h-2 rounded-full"
                style={{ backgroundColor: color }}
              ></span>
              <span className="text-sm text-black">
                {displayNameMap[item.gold_type] || item.gold_type}
              </span>
              <span
                className={isComparisonMode ? "text-[#595959]" : "text-black"}
              >
                {change !== null
                  ? isComparisonMode
                    ? change >= 0
                      ? `+${formatPercent(change)}`
                      : formatPercent(change)
                    : formatPrice(lastPrice) + "đ"
                  : "-"}
              </span>

              {isComparisonMode && (
                <button
                  onClick={() =>
                    setSelectedItems((prev) =>
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
      {!isComparisonMode && (
        <div className="text-xs text-[#595959] mb-4">
          Đơn vị: ngàn đồng/lượng
        </div>
      )}
      <HighchartsReact highcharts={Highcharts} options={options} />
    </div>
  );
}
