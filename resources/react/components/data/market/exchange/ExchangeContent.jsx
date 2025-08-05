import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchExchangeCurrent } from "@/store/market/exchangeSlice";
import ExchangeContainer from "./ExchangeContainer";
import ExchangeTable from "./ExchangeTable";
import CurrencyConverter from "./CurrencyConverter";
import SummaryBox from "@/components/data/market/shared/SummaryBox";
import { formatVietnamDateTime, formatPriceVND } from "@/lib/market/format";

export default function ExchangeContent() {
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchExchangeCurrent({ type: "market", code: "USD" }));
    dispatch(fetchExchangeCurrent({ type: "market", code: "EUR" }));
  }, [dispatch]);

  const usdVnd = useSelector((state) => state.exchange.current["market-USD"]);
  const eurVnd = useSelector((state) => state.exchange.current["market-EUR"]);

  return (
    <div className="flex w-full">
      <div className="basis-2/3">
        <div className="flex flex-col mb-4 ">
          <div className="flex gap-4">
            <SummaryBox
              label={null}
              symbol="USD"
              value={usdVnd?.rate}
              percent={usdVnd?.delta_percent}
              formatPrice={formatPriceVND}
              loading={typeof usdVnd?.rate !== "number"}
            />
            <div className="w-px bg-[#191919] opacity-100" />
            <SummaryBox
              label={null}
              symbol="EUR"
              value={eurVnd?.rate}
              percent={eurVnd?.delta_percent}
              formatPrice={formatPriceVND}
              loading={typeof eurVnd?.rate !== "number"}
            />
          </div>
          <div className="text-[#595959] text-sm mt-2">
            Cập nhật lần cuối: {formatVietnamDateTime(usdVnd?.timestamp)}
          </div>
        </div>
        <div>
          <ExchangeContainer />
        </div>
      </div>
      <div className="basis-1/3 p-2 pt-0">
        <CurrencyConverter />
        <ExchangeTable />
      </div>
    </div>
  );
}
