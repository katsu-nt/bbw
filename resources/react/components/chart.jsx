import { useEffect, useRef } from "react";
import { MinusCircle } from "lucide-react";

export default function Chart({ data, selectedTypes, setSelectedTypes, range }) {
  const chartRef = useRef(null);
  const chartInstanceRef = useRef(null);

  const colors = [
    "#000000", "#3b82f6", "#ef4444", "#22c55e",
    "#a855f7", "#f59e0b", "#14b8a6", "#6366f1",
    "#f43f5e", "#10b981",
  ];

  const formatPrice = (value) => value?.toLocaleString("vi-VN", { maximumFractionDigits: 0 });
  const formatPercent = (value) => `${value.toFixed(1)}%`;

  const getDaysFromRange = (range) => {
    switch (range) {
      case "7d": return 7;
      case "30d": return 30;
      case "6m": return 180;
      case "1y": return 365;
      case "5y": return 1825;
      case "ytd": {
        const now = new Date();
        const start = new Date(now.getFullYear(), 0, 1);
        const diff = Math.floor((now - start) / (1000 * 60 * 60 * 24));
        return diff + 1;
      }
      default: return 30;
    }
  };

  useEffect(() => {
    if (!chartRef.current || !window.Chart) return;
    const ChartJS = window.Chart;
    if (chartInstanceRef.current) chartInstanceRef.current.destroy();

    const days = getDaysFromRange(range);
    const isComparisonMode = selectedTypes.length > 1;

    const datasets = selectedTypes.map((type, index) => {
      const goldData = data[type]?.[days] || [];
      if (goldData.length === 0) return null;

      const basePrice = goldData[0]?.price;
      if (!basePrice) return null;

      return {
        label: type,
        data: goldData.map((item) => ({
          x: new Date(new Date(item.date).toISOString().split("T")[0]),
          y: isComparisonMode
            ? ((item.price - basePrice) / basePrice) * 100
            : item.price,
        })),
        borderColor: colors[index % colors.length],
        backgroundColor: colors[index % colors.length],
        tension: 0.4,
        fill: false,
        pointRadius: 0,
        pointHoverRadius: 0,
      };
    }).filter(Boolean);

    const allDates = datasets.flatMap(d => d.data.map(p => new Date(p.x))).sort((a, b) => a - b);

    const ctx = chartRef.current.getContext("2d");
    chartInstanceRef.current = new ChartJS(ctx, {
      type: "line",
      data: { datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: "nearest", intersect: false },
        plugins: {
          tooltip: {
            callbacks: {
              label: (ctx) => `${ctx.dataset.label}: ${isComparisonMode ? formatPercent(ctx.raw.y) : formatPrice(ctx.raw.y)}${isComparisonMode ? '' : '₫'}`,
            },
          },
          legend: { display: false },
        },
        scales: {
          x: {
            type: "time",
            time: {
              tooltipFormat: "yyyy-MM-dd",
              displayFormats: {
                day: "dd/MM",
                month: "MM/yyyy",
                year: "yyyy",
              },
            },
            min: allDates[0],
            max: allDates[allDates.length - 1],
            ticks: {
              maxTicksLimit: 7,
              callback: function (value, index, ticks) {
                const date = new Date(this.getLabelForValue(value));
                const latest = new Date(this.getLabelForValue(ticks[ticks.length - 1].value));
                const earliest = new Date(this.getLabelForValue(ticks[0].value));
                const spanYears = latest.getFullYear() - earliest.getFullYear();
                const isYearOnly = spanYears >= 4;
                return isYearOnly
                  ? date.getFullYear()
                  : `${date.getDate().toString().padStart(2, "0")}/${(date.getMonth() + 1).toString().padStart(2, "0")}`;
              },
            },
            grid: { display: false },
            title: { display: true, text: "Thời gian" },
          },
          y: {
            position: isComparisonMode ? "right" : "left",
            ticks: {
              callback: (val) => isComparisonMode ? formatPercent(val) : `${val / 1000}K`,
            },
            grid: { display: true },
            title: {
              display: true,
              text: isComparisonMode ? "Thay đổi (%)" : "Đơn vị: nghìn đồng/lượng",
            },
            suggestedMin: isComparisonMode ? -10 : undefined,
            suggestedMax: isComparisonMode ? 10 : undefined,
          },
        },
      },
    });
  }, [data, selectedTypes, chartRef, range]);

  return (
    <div className="w-full">
      <div className="flex flex-wrap gap-4 items-center mb-4">
        {selectedTypes.map((type, index) => {
          const days = getDaysFromRange(range);
          const goldData = data[type]?.[days] || [];
          const basePrice = goldData[0]?.price;
          const lastPrice = goldData.at(-1)?.price;
          const change = basePrice && lastPrice
            ? ((lastPrice - basePrice) / basePrice) * 100
            : null;
          const color = colors[index % colors.length];

          return (
            <div
              key={type}
              className="flex items-center gap-1 px-3 py-1 rounded-full border text-sm"
              style={{ borderColor: color }}
            >
              <span className="text-sm" style={{ color }}>{type}</span>
              <span className="font-medium">
                {change !== null
                  ? (selectedTypes.length > 1
                      ? formatPercent(change)
                      : formatPrice(lastPrice) + "₫")
                  : "-"}
              </span>
              {selectedTypes.length > 1 && (
                <button onClick={() => setSelectedTypes(selectedTypes.filter((t) => t !== type))}>
                  <MinusCircle className="w-4 h-4 ml-1 opacity-70 hover:opacity-100" />
                </button>
              )}
            </div>
          );
        })}
      </div>
      <div style={{ height: "400px" }}>
        <canvas ref={chartRef} />
      </div>
    </div>
  );
}
