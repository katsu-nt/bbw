import Highcharts from "highcharts";
import HighchartsReact from "highcharts-react-official";

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
      const t = Math.round((i * totalDays) / (maxTicks - 1));
      ticks.push(startDate.getTime() + t * dayMs);
    }
  }
  return ticks;
}

export default function MultiLineChart({
  items = [],
  data,
  range,
  mode,
  options,
  setItems,
  seriesAdapter, // Function
  yAxesAdapter, // Function
  tooltipFormatter, // Function
  unitSideText,
  renderLegendItem,
  chartColors,
  chartHeight = 400,
}) {
  const { series, allDates, spanYears } = seriesAdapter({
    items,
    data,
    range,
    mode,
    options,
    chartColors,
  });

  // XAxis ticks
  const sortedDates = [...new Set(allDates)].sort((a, b) => a - b);
  let xTickPositions = [];
  let _spanYears = spanYears;

  if (sortedDates.length > 0) {
    const minDate = new Date(sortedDates[0]);
    const maxDate = new Date(sortedDates[sortedDates.length - 1]);
    _spanYears = maxDate.getFullYear() - minDate.getFullYear();

    if (range === "7d") {
      // Đảm bảo hiện đủ mọi ngày liên tục từ min -> max
      xTickPositions = [];
      const dayMs = 24 * 3600 * 1000;
      for (let t = minDate.getTime(); t <= maxDate.getTime(); t += dayMs) {
        xTickPositions.push(t);
      }
    } else {
      xTickPositions = getDateRangeTicks(minDate, maxDate, 5);
    }
  }

  // yAxis
  const yAxis = yAxesAdapter({ items, data, range, mode, options });

  // Chart options
  const optionsChart = {
    chart: { type: "line", zoomType: "x", height: chartHeight },
    title: { text: null },
    xAxis: {
      type: "datetime",
      title: { text: null },
      crosshair: { width: 1, color: "#ccc", dashStyle: "ShortDot" },
      tickPositions: xTickPositions,
      labels: {
        formatter: function () {
          const date = new Date(this.value);
          return _spanYears >= 3
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
      useHTML: true, // Quan trọng cho tooltip custom
      formatter: function () {
        return tooltipFormatter(this, mode); // Dùng đúng prop truyền vào!
      },
    },
    legend: { enabled: false },
    credits: { enabled: false },
    series: series,
  };

  return (
    <div className="w-full min-h-[440px]">
      <div className="flex flex-wrap gap-4 items-center mb-4">
        {items.map((item, idx) =>
          renderLegendItem({
            item,
            idx,
            color: chartColors[idx % chartColors.length],
            items,
            setItems,
            data,
            range,
            mode,
            options,
          })
        )}
      </div>
      {unitSideText && (
        <div className="text-xs text-[#595959] mb-4">{unitSideText}</div>
      )}
      <HighchartsReact highcharts={Highcharts} options={optionsChart} />
    </div>
  );
}
