import React, { useState, useEffect } from "react";
import DatePicker from "@/components/ui/datePicker";
import ThreeDotsWave from "@/components/ui/threeDotsWave";
import { ResponseStatus } from "@/components/ui/responseStatus";
import { useDispatch, useSelector } from "react-redux";
import { fetchExchangeTable } from "@/store/market/exchangeSlice";

function formatRate(value) {
  return typeof value === "number"
    ? value.toLocaleString("vi-VN", { maximumFractionDigits: 2 })
    : "Đang cập nhật";
}
function formatDelta(delta) {
  if (delta === null || delta === undefined) return null;
  const value = Math.round(delta * 100) / 100;
  return value > 0 ? `+${value}` : `${value}`;
}
function formatDateLocal(date) {
  const tzOffset = date.getTimezoneOffset() * 60000;
  const localISO = new Date(date.getTime() - tzOffset).toISOString();
  return localISO.split("T")[0];
}

export default function ExchangeTable() {
  const dispatch = useDispatch();
  const [selectedDate, setSelectedDate] = useState(new Date());

  const tableData = useSelector((state) => state.exchange.table);
  const loading = useSelector((state) => state.exchange.loading.table);
  const error = useSelector((state) => state.exchange.error.table);

  useEffect(() => {
    const dateStr = formatDateLocal(selectedDate);
    dispatch(fetchExchangeTable({ type: "market", date: dateStr }));
  }, [dispatch, selectedDate]);

  // Chỉ hiện mã base, vì mặc định là VND
  const allCodes = ["USD", "EUR", "JPY", "CNY"];

  function findRate(code) {
    return tableData.find((item) => item.code === code);
  }

  return (
    <div className="flex flex-col gap-3">
      <div>
        <DatePicker value={selectedDate} onChange={setSelectedDate} />
      </div>
      <div className="border rounded-md border-[#E7E7E7] shadow overflow-hidden">
        <div className="grid grid-cols-[1.5fr_1fr] items-center bg-[#FAFAFA] text-left text-black text-sm font-semibold p-4 pr-6 border-b border-[#D5D7DA]">
          <div className="py-2 sticky top-0 z-10 flex items-center">Mã ngoại tệ</div>
          <div className="py-2 sticky top-0 z-10">
            <div className="flex flex-col">
              <span>Tỉ giá</span>
              <span className="text-xs text-[#595959] font-medium">
                Đơn vị: VND
              </span>
            </div>
          </div>
        </div>
        <div
          className="h-[315px] overflow-y-auto divide-y divide-[#D5D7DA] relative"
          style={{
            scrollbarWidth: "thin",
            scrollbarColor: "#ECECEC transparent",
          }}
        >
          <style>{`
            div::-webkit-scrollbar { width: 6px; }
            div::-webkit-scrollbar-thumb { background-color: #ececec; border-radius: 4px; }
            div::-webkit-scrollbar-button { display: none; height: 0; }
          `}</style>
          {loading ? (
            <div className="absolute inset-0 flex items-center justify-center text-sm text-[#595959]">
              <ResponseStatus status="loading" message="Đang tải dữ liệu..." />
            </div>
          ) : error ? (
            <div className="absolute inset-0 flex items-center justify-center text-sm text-[#B51001]">
              <ResponseStatus status="error" message="Không thể kết nối đến máy chủ" />
            </div>
          ) : (
            allCodes.map((code) => {
              const item = findRate(code);
              return (
                <div
                  key={code}
                  className="grid grid-cols-[1.5fr_1fr] min-h-[86px] text-[#191919] text-sm text-left px-4 py-2 items-center"
                >
                  <div className="truncate pr-2">{code}</div>
                  <div>
                    {item ? (
                      <div className="flex flex-col gap-1">
                        <span>{formatRate(item.rate)}</span>
                        {item.delta !== undefined && item.delta !== null && item.delta !== 0 && (
                          <span
                            className={
                              item.delta > 0
                                ? "text-[#00DC3C]"
                                : item.delta < 0
                                ? "text-[#B51001]"
                                : ""
                            }
                            style={{ fontSize: 12 }}
                          >
                            {formatDelta(item.delta)}
                          </span>
                        )}
                      </div>
                    ) : (
                      <div className="w-full flex justify-start">
                        <ThreeDotsWave size="8px" color="#191919" />
                      </div>
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
