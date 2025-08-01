import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchGoldCurrent } from "@/store/market/goldSlice";
import ThreeDotsWave from "@/components/ui/threeDotsWave"
import GoldContainer from "./GoldContainer";
import GoldTable from "./GoldTable";

export default function GoldContent() {
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchGoldCurrent({ gold_type: "xau_usd", location: "global" }));
    dispatch(fetchGoldCurrent({ gold_type: "sjc", location: "hcm" }));
  }, [dispatch]);

  const xau = useSelector((state) => state.gold.current["xau_usd-global"]);
  const sjc = useSelector((state) => state.gold.current["sjc-hcm"]);

  const formatPriceVND = (price) =>
    typeof price === "number" ? price.toLocaleString("vi-VN")+ " VND" : <ThreeDotsWave size="8px" color="#191919" />;

  const formatPriceUSD = (price) =>
    typeof price === "number" ? price.toLocaleString("en-US")+ " USD" : <ThreeDotsWave size="8px" color="#191919" />;

  const formatPercent = (delta) =>
    typeof delta === "number"
      ? `${delta > 0 ? "+" : ""}${delta.toFixed(2)}%`
      : "";
  const formatVietnamDateTime = (isoString) => {
    if (!isoString) return "";
    const date = new Date(isoString);
    return date.toLocaleString("vi-VN", {
      hour12: false,
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      timeZone: "Asia/Ho_Chi_Minh", 
    });
  };
  return (
    <>
      <div className="flex flex-col mb-4">
        <div className="flex gap-4">
          <div className="flex flex-col">
            <div className="text-lg font-normal text-[#191919]">
              Giá vàng thế giới
            </div>
            <div className="text-2xl font-medium text-[#191919] flex gap-2 items-center">
              <span>XAU/USD</span>
              <span>{formatPriceUSD(xau?.sell_price)}</span>
              {typeof xau?.delta_percent === "number" &&
                xau.delta_percent !== 0 && (
                  <span
                    className={`flex items-center ${
                      xau.delta_percent > 0
                        ? "text-[#00B14F]"
                        : "text-[#B51001]"
                    }`}
                  >
                    <span className="text-sm inline-block -translate-y-2 transform">
                      {xau.delta_percent > 0 ? "▲" : "▼"}
                    </span>
                    <span>{formatPercent(xau.delta_percent)}</span>
                  </span>
                )}
            </div>
          </div>

          <div className="w-px bg-[#191919] opacity-100" />

          <div className="flex flex-col">
            <div className="text-lg font-normal text-[#191919]">
              Giá vàng trong nước
            </div>
            <div className="text-2xl font-medium text-[#191919] flex gap-2 items-center">
              <span>SJC</span>
              <span>
                {formatPriceVND(sjc ? sjc.sell_price * 1000 : undefined)}
              </span>
              {typeof sjc?.delta_percent === "number" &&
                sjc.delta_percent !== 0 && (
                  <span
                    className={`flex items-center ${
                      sjc.delta_percent > 0
                        ? "text-[#00B14F]"
                        : "text-[#B51001]"
                    }`}
                  >
                    <span className="text-sm inline-block -translate-y-2 transform">
                      {sjc.delta_percent > 0 ? "▲" : "▼"}
                    </span>
                    <span>{formatPercent(sjc.delta_percent)}</span>
                  </span>
                )}
            </div>
          </div>
        </div>
        <div className="text-[#595959] text-sm mt-2">
          Cập nhật lần cuối: {" "}{formatVietnamDateTime(sjc?.timestamp)}
        </div>
      </div>

      <div className="flex w-full">
        <div className="basis-2/3 p-2 pl-0">
          <GoldContainer />
        </div>
        <div className="basis-1/3 p-2">
          <GoldTable />
        </div>
      </div>
    </>
  );
}
