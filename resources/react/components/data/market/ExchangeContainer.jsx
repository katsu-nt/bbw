import { useState, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from "@/components/ui/dropdown-menu";
import { Search, ChevronDown } from "lucide-react";
import { fetchExchangeChart } from "@/store/market/exchangeSlice";
import ExchangeChart from "./ExchangeChart";
import { ResponseStatus } from "@/components/ui/responseStatus";
import CompareSwitch from "./CompareSwitch";

// Danh sách mã + loại cho Dropdown
const exchangeOptions = [
  { code: "USD", type: "central", name: "USD (SBVN)" },
  { code: "USD", type: "market", name: "USD" },
  { code: "DXY", type: "index", name: "DXY" },
  // Thêm các mã khác nếu muốn
];

const ranges = [
  { label: "1 Tuần", value: "7d" },
  { label: "1 Tháng", value: "30d" },
  { label: "6 Tháng", value: "6m" },
  { label: "YTD", value: "ytd" },
  { label: "1 Năm", value: "1y" },
  { label: "5 Năm", value: "5y" },
];

function getDaysFromRange(range) {
  const today = new Date();
  switch (range) {
    case "7d":
      return 7;
    case "30d":
      return 30;
    case "6m":
      return 180;
    case "1y":
      return 365;
    case "5y":
      return 365 * 5;
    case "ytd": {
      const start = new Date(today.getFullYear(), 0, 1);
      return Math.floor((today - start) / (1000 * 60 * 60 * 24)) + 1;
    }
    default:
      return 30;
  }
}

export default function ExchangeContainer() {
  // mode: "default" | "normalize"
  const [mode, setMode] = useState("default");
  // Nhiều mã (luôn là mảng), khởi tạo với USD/market
  const [selectedItems, setSelectedItems] = useState([
    { code: "USD", type: "market" }
  ]);
  const [range, setRange] = useState("7d");

  const dispatch = useDispatch();
  const {
    chart: data,
    loading,
    error,
  } = useSelector((state) => state.exchange);
  const days = getDaysFromRange(range);

  // Filter các option chưa được chọn để show trên Dropdown
  const filteredOptions = exchangeOptions.filter(
    (item) =>
      !selectedItems.some(
        (s) => s.code === item.code && s.type === item.type
      )
  );

  // Fetch dữ liệu cho tất cả các mã đang chọn
  useEffect(() => {
    selectedItems.forEach(({ type, code }) => {
      const key = `${type}-${code}`;
      if (!data[key] || !data[key][days]) {
        dispatch(
          fetchExchangeChart({
            type,
            code: [code],
            days,
          })
        );
      }
    });
  }, [selectedItems, range, data, dispatch, days]);

  // Handler thêm mã mới vào so sánh
  function handleAddItem(item) {
    setSelectedItems((prev) => [...prev, item]);
  }

  // Handler xóa mã khỏi chart
  function handleRemoveItem(item) {
    setSelectedItems((prev) =>
      prev.filter(
        (x) => x.code !== item.code || x.type !== item.type
      )
    );
  }

  // Chuyển mode default/normalize
  function handleSwitchMode() {
    setMode(mode === "default" ? "normalize" : "default");
  }

  // Nếu không còn mã nào thì luôn có ít nhất 1 mã (USD/market)
  useEffect(() => {
    if (selectedItems.length === 0) {
      setSelectedItems([{ code: "USD", type: "market" }]);
    }
  }, [selectedItems]);

  return (
    <div className="border rounded-md border-[#E7E7E7] shadow p-6 min-h-[586px]">
      <Tabs defaultValue={range} onValueChange={setRange} className="w-full">
        <div className="flex justify-between items-center mb-4">
          <TabsList className="inline-flex h-[40px] bg-[#FAFAFA] rounded-lg shadow-[inset_0_0_0_1px_#E7E7E7] overflow-hidden">
            {ranges.map((tab) => (
              <TabsTrigger
                key={tab.value}
                value={tab.value}
                className={`text-sm font-semibold h-full px-4 py-2 text-[#989898] border border-transparent focus:outline-none data-[state=active]:text-black data-[state=active]:bg-white  data-[state=active]:border-[#D5D7DA] data-[state=active]:z-10 first:data-[state=active]:rounded-l-lg last:data-[state=active]:rounded-r-lg`}
              >
                {tab.label}
              </TabsTrigger>
            ))}
          </TabsList>
          <div className="flex items-center gap-2">
            {/* Dropdown thêm mã */}
            <DropdownMenu>
              <DropdownMenuTrigger asChild className="min-w-[220px] h-[40px]">
                <button className="relative flex items-center justify-between gap-2 px-4 py-2 border border-[#D5D7DA] rounded-lg shadow text-[#595959] text-base w-[220px] bg-white">
                  <div className="flex items-center gap-2 text-base">
                    <Search className="w-4 h-4 text-[#A4A7AE]" />
                    Thêm mã so sánh
                  </div>
                  <ChevronDown className="w-4 h-4 text-[#BBBBBB]" />
                </button>
              </DropdownMenuTrigger>
              <DropdownMenuContent
                align="end"
                sideOffset={4}
                className="z-50 mt-1 max-h-[300px] overflow-y-auto w-[--radix-popper-anchor-width] rounded-md border border-[#E7E7E7] bg-white shadow"
              >
                {filteredOptions.length === 0 ? (
                  <DropdownMenuItem disabled>
                    Không còn mã nào
                  </DropdownMenuItem>
                ) : (
                  filteredOptions.map((item) => (
                    <DropdownMenuItem
                      key={item.type + item.code}
                      onClick={() => handleAddItem(item)}
                      className="text-sm px-3 py-2 cursor-pointer"
                    >
                      {item.name}
                    </DropdownMenuItem>
                  ))
                )}
              </DropdownMenuContent>
            </DropdownMenu>
            <CompareSwitch
              checked={mode === "normalize"}
              onChange={handleSwitchMode}
            />
          </div>
        </div>
        <TabsContent value={range}>
          <div className="w-full transition-all">
            {error.chart ? (
              <div className="w-full space-y-6">
                <div className="flex flex-wrap gap-3 items-center">
                  {[...Array(3)].map((_, i) => (
                    <div
                      key={i}
                      className="h-8 w-32 rounded-full bg-gray-200"
                    />
                  ))}
                </div>
                <div className="relative w-full h-[400px] rounded-md bg-gray-100 border border-gray-200 flex items-center justify-center">
                  <ResponseStatus
                    status="error"
                    message="Không thể kết nối đến máy chủ"
                  />
                </div>
              </div>
            ) : loading.chart ||
              selectedItems.some(({ code, type }) => {
                const key = `${type}-${code}`;
                const arr = data[key]?.[days] || [];
                return arr.length === 0;
              }) ? (
              <div className="w-full space-y-6">
                <div className="flex flex-wrap gap-3 items-center">
                  {[...Array(3)].map((_, i) => (
                    <div
                      key={i}
                      className="h-8 w-32 rounded-full bg-gray-200"
                    />
                  ))}
                </div>
                <div className="relative w-full h-[400px] rounded-md bg-gray-100 border border-gray-200 flex items-center justify-center">
                  <ResponseStatus
                    status="loading"
                    message="Đang tải dữ liệu..."
                  />
                </div>
              </div>
            ) : (
              <ExchangeChart
                mode={mode}
                chartItems={selectedItems}
                setChartItems={setSelectedItems}
                data={data}
                range={range}
                options={exchangeOptions}
              />
            )}
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}
