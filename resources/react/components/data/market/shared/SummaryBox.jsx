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
        {typeof percent === "number" && percent !== 0 && (
          <span
            className={`flex items-center ${
              percent > 0 ? "text-[#00DC3C]" : "text-[#B51001]"
            }`}
          >
            <span className="text-sm inline-block -translate-y-2 transform">
              {percent > 0 ? "▲" : "▼"}
            </span>
            <span>
              {`${percent > 0 ? "+" : ""}${percent.toFixed(2)}%`}
            </span>
          </span>
        )}
      </div>
    </div>
  );
}
