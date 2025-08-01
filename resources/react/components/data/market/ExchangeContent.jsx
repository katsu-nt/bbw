import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchExchangeCurrent } from "@/store/market/exchangeSlice";
import ExchangeContainer from "./ExchangeContainer";
import ExchangeTable from "./ExchangeTable";
import ThreeDotsWave from "@/components/ui/threeDotsWave";
import CurrencyConverter from "./CurrencyConverter";

export default function ExchangeContent() {
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchExchangeCurrent({ type: "market", code: "USD" }));
    dispatch(fetchExchangeCurrent({ type: "market", code: "EUR" }));
  }, [dispatch]);

  const usdVnd = useSelector((state) => state.exchange.current["market-USD"]);
  const eurVnd = useSelector((state) => state.exchange.current["market-EUR"]);

  const formatPrice = (rate) =>
    typeof rate === "number" ? (
      rate.toLocaleString("vi-VN", { maximumFractionDigits: 2 }) + " VND"
    ) : (
      <ThreeDotsWave size="8px" color="#191919" />
    );
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
      <div className="flex w-full">
        <div className="basis-2/3">
          <div className="flex flex-col mb-4 ">
            <div className="flex gap-4">
              <div className="flex flex-col">
                <div className="text-2xl font-medium text-[#191919] flex gap-2 items-center">
                  <span>USD</span>
                  <span>{formatPrice(usdVnd?.rate)}</span>
                  {typeof usdVnd?.delta_percent === "number" &&
                    usdVnd.delta_percent !== 0 && (
                      <span
                        className={`flex items-center ${
                          usdVnd.delta_percent > 0
                            ? "text-[#00B14F]"
                            : "text-[#B51001]"
                        }`}
                      >
                        <span className="text-sm inline-block -translate-y-2 transform">
                          {usdVnd.delta_percent > 0 ? "▲" : "▼"}
                        </span>
                        <span>{formatPercent(usdVnd.delta_percent)}</span>
                      </span>
                    )}
                </div>
              </div>
              <div className="w-px bg-[#191919] opacity-100" />
              <div className="flex flex-col">
                <div className="text-2xl font-medium text-[#191919] flex gap-2 items-center">
                  <span>EUR</span>
                  <span>{formatPrice(eurVnd?.rate)}</span>
                  {typeof eurVnd?.delta_percent === "number" &&
                    eurVnd.delta_percent !== 0 && (
                      <span
                        className={`flex items-center ${
                          eurVnd.delta_percent > 0
                            ? "text-[#00B14F]"
                            : "text-[#B51001]"
                        }`}
                      >
                        <span className="text-sm inline-block -translate-y-2 transform">
                          {eurVnd.delta_percent > 0 ? "▲" : "▼"}
                        </span>
                        <span>{formatPercent(eurVnd.delta_percent)}</span>
                      </span>
                    )}
                </div>
              </div>
            </div>
            <div className="text-[#595959] text-sm mt-2">
              Cập nhật lần cuối: {formatVietnamDateTime(usdVnd?.timestamp)}
            </div>
          </div>

          <div className="">
            <ExchangeContainer />
          </div>
        </div>

        <div className="basis-1/3 p-2 pt-0">
          <CurrencyConverter />
          <ExchangeTable />
        </div>
      </div>
    </>
  );
}
