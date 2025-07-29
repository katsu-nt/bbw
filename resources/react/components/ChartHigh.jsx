import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";
import { MinusCircle } from "lucide-react";

const colors = [
  "#000000",
  "#3b82f6",
  "#ef4444",
  "#22c55e",
  "#a855f7",
  "#f59e0b",
  "#14b8a6",
  "#6366f1",
  "#f43f5e",
  "#10b981",
];

const formatPrice = (value) =>
  value?.toLocaleString("vi-VN", { maximumFractionDigits: 0 });

const formatPercent = (value) => `${value.toFixed(1)}%`;

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

export default function ChartHigh({
  data,
  selectedTypes,
  setSelectedTypes,
  range,
}) {
  const days = getDaysFromRange(range);
  const isComparisonMode = selectedTypes.length > 1;

  const allDates = [];

  const series = selectedTypes
    .map((type, index) => {
      const goldData = data[type]?.[days] || [];
      if (goldData.length === 0) return null;

      const basePrice = goldData[0].price;
      const color = colors[index % colors.length];

      const seriesData = goldData.map((item) => {
        const timestamp = new Date(item.date).getTime();
        allDates.push(timestamp);
        return [
          timestamp,
          isComparisonMode
            ? ((item.price - basePrice) / basePrice) * 100
            : item.price,
        ];
      });

      return {
        name: type,
        data: seriesData,
        color,
        tooltip: {
          valueSuffix: isComparisonMode ? "%" : "₫",
          valueDecimals: isComparisonMode ? 1 : 0,
        },
      };
    })
    .filter(Boolean);

  const sortedDates = allDates.sort((a, b) => a - b);
  const minDate = sortedDates[0];
  const maxDate = sortedDates[sortedDates.length - 1];

  const yearStart = new Date(minDate).getFullYear();
  const yearEnd = new Date(maxDate).getFullYear();
  const spanYears = yearEnd - yearStart;

  // 👉 Custom Y ticks: ±max(abs(min), abs(max)), làm tròn lên gần nhất 10
  let yTickPositions;
  if (isComparisonMode) {
    const allY = series.flatMap((s) => s.data.map(([_, y]) => y));
    const yMin = Math.min(...allY);
    const yMax = Math.max(...allY);
    const absMax = Math.max(Math.abs(yMin), Math.abs(yMax));
    const rounded = Math.ceil(absMax / 10) * 10; // làm tròn lên mốc gần nhất chia hết cho 10
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
      title: { text: "Thời gian" },
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
      title: {
        text: isComparisonMode ? "Thay đổi (%)" : "Đơn vị: nghìn đồng/lượng",
      },
      labels: {
        formatter: function () {
          return isComparisonMode
            ? `${this.value.toFixed(1)}%`
            : `${(this.value / 1000).toFixed(0)}K`;
        },
      },
      tickPositions: isComparisonMode ? yTickPositions : undefined,
    },
    tooltip: {
      shared: true,
      xDateFormat: "%d/%m/%Y",
    },
    legend: { enabled: false },
    series,
    credits: { enabled: false },
  };

  return (
    <div className="w-full">
      <div className="flex flex-wrap gap-4 items-center mb-4">
        {selectedTypes.map((type, index) => {
          const color = colors[index % colors.length];
          const goldData = data[type]?.[days] || [];
          const basePrice = goldData[0]?.price;
          const lastPrice = goldData.at(-1)?.price;
          const change =
            basePrice && lastPrice
              ? ((lastPrice - basePrice) / basePrice) * 100
              : null;

          return (
            <div
              key={type}
              className="flex items-center gap-1 px-3 py-1 rounded-full border text-sm"
              style={{ borderColor: color }}
            >
              <span className="text-sm" style={{ color }}>
                {type}
              </span>
              <span className="font-medium">
                {change !== null
                  ? isComparisonMode
                    ? formatPercent(change)
                    : formatPrice(lastPrice) + "₫"
                  : "-"}
              </span>
              {isComparisonMode && (
                <button
                  onClick={() =>
                    setSelectedTypes(selectedTypes.filter((t) => t !== type))
                  }
                >
                  <MinusCircle className="w-4 h-4 ml-1 opacity-70 hover:opacity-100" />
                </button>
              )}
            </div>
          );
        })}
      </div>

      <HighchartsReact highcharts={Highcharts} options={options} />
    </div>
  );
}
