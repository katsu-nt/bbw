import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { fetchGoldCurrent } from "@/store/market/goldSlice";
import GoldContainer from "./GoldContainer";
import GoldTable from "./GoldTable";
import SummaryBox from "@/components/data/market/shared/SummaryBox";
import { formatVietnamDateTime, formatPriceVND, formatPriceUSD } from "@/lib/market/format";

export default function GoldContent() {
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(fetchGoldCurrent({ gold_type: "xau_usd", location: "global", unit: "ounce" }));
    dispatch(fetchGoldCurrent({ gold_type: "sjc", location: "hcm" }));
  }, [dispatch]);

  const xau = useSelector((state) => state.gold.current["xau_usd-global"])?.[0];
  const sjc = useSelector((state) => state.gold.current["sjc-hcm"])?.[0];

  return (
    <>
      <div className="flex flex-col mb-4 px-4 md:px-0">
        <div className="flex flex-col md:flex-row gap-4">
          <SummaryBox
            label="Giá vàng thế giới"
            symbol="XAU/USD"
            value={xau?.sell_price}
            percent={xau?.delta_sell_percent}
            formatPrice={formatPriceUSD}
            loading={typeof xau?.sell_price !== "number"}
          />
          <div className="hidden md:block w-px bg-[#191919] opacity-100" />
          <SummaryBox
            label="Giá vàng trong nước"
            symbol="SJC"
            value={sjc ? sjc.sell_price * 1000 : undefined}
            percent={sjc?.delta_sell_percent}
            formatPrice={formatPriceVND}
            loading={typeof sjc?.sell_price !== "number"}
          />
        </div>
        <div className="text-[#595959] text-sm mt-2">
          Cập nhật lần cuối: {formatVietnamDateTime(sjc?.timestamp)}
        </div>
      </div>
      <div className="flex flex-col md:flex-row w-full">
        <div className="md:basis-2/3 w-full p-2 md:pl-0">
          <GoldContainer />
        </div>
        <div className="md:basis-1/3 w-full p-2">
          <GoldTable />
        </div>
      </div>
    </>
  );
}
