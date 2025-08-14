import ThreeDotsWave from "@/components/ui/threeDotsWave";

export default function SummaryBox({
  label,
  symbol,
  value,
  percent,
  formatPrice,
  colorUp = "#00DC3C",
  colorDown = "#B51001",
  loading = false,
}) {
  const showPercent = typeof percent === "number" && percent !== 0;
  const isUp = showPercent && percent > 0;

  return (
    <div className="flex flex-col">
      {label && (
        <div className="text-base md:text-lg font-normal text-[#191919]">
          {label}
        </div>
      )}
      <div className="text-lg md:text-2xl font-medium text-[#191919] flex gap-2 items-center">
        <span>{symbol}</span>
        <span>
          {loading ? (
            <ThreeDotsWave size="8px" color="#191919" />
          ) : (
            formatPrice(value)
          )}
        </span>

        {showPercent && (
          <span
            className={`flex items-center ${isUp ? "text-[#00DC3C]" : "text-[#B51001]"
              }`}
            style={{ color: isUp ? colorUp : colorDown }}
          >
            <span className="text-sm inline-block -translate-y-2 transform">
              {isUp ? "▲" : "▼"}
            </span>
            <span>{`${Math.abs(percent).toFixed(2)}%`}</span>
          </span>
        )}
      </div>
    </div>
  );
}
