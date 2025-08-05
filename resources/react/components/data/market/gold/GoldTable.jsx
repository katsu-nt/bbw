import React, { useState, useEffect } from "react";
import DatePicker from "@/components/ui/datePicker";
import ThreeDotsWave from "@/components/ui/threeDotsWave";
import { ResponseStatus } from "@/components/ui/responseStatus";
import { useDispatch, useSelector } from "react-redux";
import { fetchGoldTable } from "@/store/market/goldSlice";

function formatPrice(value) {
  return typeof value === "number"
    ? value.toLocaleString("vi-VN")
    : "Đang cập nhật";
}

function formatDelta(delta) {
  if (delta === null || delta === undefined) return null;
  const value = Math.round(delta);
  return value > 0
    ? `+${value.toLocaleString("vi-VN")}`
    : value.toLocaleString("vi-VN");
}

const FIXED_BRANDS = [
  "PNJ - HCM",
  "SJC - HCM",
  "PNJ - HN",
  "SJC - HN",
  "Nhẫn Trơn PNJ 999.9",
  "Vàng Kim Bảo 999.9",
  "Vàng Phúc Lộc Tài 999.9",
  "Vàng nữ trang 999.9",
  "Vàng nữ trang 999",
  "Vàng nữ trang 9920",
  "Vàng nữ trang 99",
  "Vàng 916 (22K)",
  "Vàng 750 (18K)",
  "Vàng 680 (16.3K)",
  "Vàng 650 (15.6K)",
  "Vàng 610 (14.6K)",
  "Vàng 585 (14K)",
  "Vàng 416 (10K)",
  "Vàng 375 (9K)",
  "Vàng 333 (8K)",
];

const BRAND_MAPPING = {
  "PNJ - HCM": { gold_type: "pnj", location: "hcm" },
  "SJC - HCM": { gold_type: "sjc", location: "hcm" },
  "PNJ - HN": { gold_type: "pnj", location: "hn" },
  "SJC - HN": { gold_type: "sjc", location: "hn" },
  "Nhẫn Trơn PNJ 999.9": { gold_type: "nhẫn_trơn_pnj_9999", location: "tq" },
  "Vàng Kim Bảo 999.9": { gold_type: "vàng_kim_bảo_9999", location: "tq" },
  "Vàng Phúc Lộc Tài 999.9": {
    gold_type: "vàng_phúc_lộc_tài_9999",
    location: "tq",
  },
  "Vàng nữ trang 999.9": { gold_type: "vàng_nữ_trang_9999", location: "tq" },
  "Vàng nữ trang 999": { gold_type: "vàng_nữ_trang_999", location: "tq" },
  "Vàng nữ trang 9920": { gold_type: "vàng_nữ_trang_9920", location: "tq" },
  "Vàng nữ trang 99": { gold_type: "vàng_nữ_trang_99", location: "tq" },
  "Vàng 916 (22K)": { gold_type: "vàng_916_22k", location: "tq" },
  "Vàng 750 (18K)": { gold_type: "vàng_750_18k", location: "tq" },
  "Vàng 680 (16.3K)": { gold_type: "vàng_680_163k", location: "tq" },
  "Vàng 650 (15.6K)": { gold_type: "vàng_650_156k", location: "tq" },
  "Vàng 610 (14.6K)": { gold_type: "vàng_610_146k", location: "tq" },
  "Vàng 585 (14K)": { gold_type: "vàng_585_14k", location: "tq" },
  "Vàng 416 (10K)": { gold_type: "vàng_416_10k", location: "tq" },
  "Vàng 375 (9K)": { gold_type: "vàng_375_9k", location: "tq" },
  "Vàng 333 (8K)": { gold_type: "vàng_333_8k", location: "tq" },
};
function formatDateLocal(date) {
  const tzOffset = date.getTimezoneOffset() * 60000;
  const localISO = new Date(date.getTime() - tzOffset).toISOString();
  return localISO.split("T")[0];
}

export default function GoldTable() {
  const dispatch = useDispatch();
  const [selectedDate, setSelectedDate] = useState(new Date());

  const tableData = useSelector((state) => state.gold.table);
  const loading = useSelector((state) => state.gold.loading.table);
  const error = useSelector((state) => state.gold.error.table);

  useEffect(() => {
    const dateStr = formatDateLocal(selectedDate);
    dispatch(fetchGoldTable(dateStr));
  }, [dispatch, selectedDate]);

  const findPrice = (brand) => {
    const config = BRAND_MAPPING[brand];
    if (!config) return null;
    return tableData.find(
      (item) =>
        item.gold_type === config.gold_type && item.location === config.location
    );
  };

  return (
    <div className="flex flex-col gap-3">
      <div>
        <DatePicker value={selectedDate} onChange={setSelectedDate} />
      </div>
      <div className="border rounded-md border-[#E7E7E7] shadow overflow-hidden">
        <div className="grid grid-cols-[1.5fr_1fr_1fr] items-center bg-[#FAFAFA] text-left text-black text-sm font-semibold p-4 pr-6 border-b border-[#D5D7DA]">
          <div className="py-2 sticky top-0 z-10 flex items-center">
            Thương hiệu
          </div>
          <div className="py-2 sticky top-0 z-10">
            <div className="flex flex-col">
              <span>Giá mua vào</span>
              <span className="text-xs text-[#595959] font-medium">
                ngàn đồng/lượng
              </span>
            </div>
          </div>
          <div className="py-2 sticky top-0 z-10">
            <div className="flex flex-col">
              <span>Giá bán ra</span>
              <span className="text-xs text-[#595959] font-medium">
                ngàn đồng/lượng
              </span>
            </div>
          </div>
        </div>

        <div
          className="h-[453px] overflow-y-auto divide-y divide-[#D5D7DA] relative"
          style={{
            scrollbarWidth: "thin",
            scrollbarColor: "#ECECEC transparent",
          }}
        >
          <style>{`
            div::-webkit-scrollbar {
              width: 6px;
            }
            div::-webkit-scrollbar-thumb {
              background-color: #ececec;
              border-radius: 4px;
            }
            div::-webkit-scrollbar-button {
              display: none;
              height: 0;
            }
          `}</style>

          {loading ? (
            <div className="absolute inset-0 flex items-center justify-center text-sm text-[#595959]">
              <ResponseStatus status="loading" message="Đang tải dữ liệu..." />
            </div>
          ) : error ? (
            <div className="absolute inset-0 flex items-center justify-center text-sm text-[#B51001]">
              <ResponseStatus
                status="error"
                message="Không thể kết nối đến máy chủ"
              />
            </div>
          ) : (
            FIXED_BRANDS.map((brand) => {
              const item = findPrice(brand);
              return (
                <div
                  key={brand}
                  className="grid grid-cols-[1.5fr_1fr_1fr] min-h-[86px] text-[#191919] text-sm text-left px-4 py-2 items-center"
                >
                  <div
                    className="truncate hover:cursor-pointer pr-2"
                    title={brand}
                  >
                    {brand}
                  </div>

                  <div className="text-left">
                    {item ? (
                      <>
                        {formatPrice(item.buy_price)}
                        {item.delta_buy !== 0 && item.delta_buy != null && (
                          <div
                            className={
                              item.delta_buy > 0
                                ? "text-[#00DC3C]"
                                : "text-[#B51001]"
                            }
                          >
                            {formatDelta(item.delta_buy)}
                          </div>
                        )}
                      </>
                    ) : (
                      <div className="w-full flex justify-center"><ThreeDotsWave size="8px" color="#191919" /></div>
                    )}
                  </div>

                  <div className="text-left">
                    {item ? (
                      <>
                        {formatPrice(item.sell_price)}
                        {item.delta_sell !== 0 && item.delta_sell != null && (
                          <div
                            className={
                              item.delta_sell > 0
                                ? "text-[#00DC3C]"
                                : "text-[#B51001]"
                            }
                          >
                            {formatDelta(item.delta_sell)}
                          </div>
                        )}
                      </>
                    ) : (
                      <div className="w-full flex justify-center"><ThreeDotsWave size="8px" color="#191919" /></div>
                    )}
                  </div>
                </div>
              );
            })
          )}
        </div>
      </div>
    </div>
  );
}
