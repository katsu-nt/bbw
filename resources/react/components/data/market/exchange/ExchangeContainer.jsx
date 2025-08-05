import { useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Tabs, TabsContent } from "@/components/ui/tabs";
import { fetchExchangeChart } from "@/store/market/exchangeSlice";
import ExchangeChart from "./ExchangeChart";
import CompareSwitch from "@/components/data/market/shared/CompareSwitch";
import RangeTabs from "@/components/data/market/shared/RangeTabs";
import AddItemDropdown from "@/components/data/market/shared/AddItemDropdown";
import ChartWrapper from "@/components/data/market/shared/ChartWrapper";
import { getDaysFromRange } from "@/lib/market/date";
import { useChartData } from "@/hooks/useChartData";

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

export default function ExchangeContainer() {
  const [mode, setMode] = useState("default");
  const [selectedItems, setSelectedItems] = useState([
    { code: "USD", type: "market" },
  ]);
  const [range, setRange] = useState("7d");

  const dispatch = useDispatch();
  const {
    chart: data,
    loading,
    error,
  } = useSelector((state) => state.exchange);
  const days = getDaysFromRange(range);

  // Custom hook dùng cho fetch chart data
  useChartData({
    items: selectedItems,
    data,
    days,
    dispatch,
    fetchAction: fetchExchangeChart,
    getKey: ({ type, code }) => `${type}-${code}`,
    getParams: (arr, days) => ({
      type: arr[0]?.type, // Giả định: mỗi lần fetch chỉ 1 loại type (có thể điều chỉnh)
      code: arr.map((i) => i.code),
      days,
    }),
  });

  // Nếu không còn mã nào thì luôn có ít nhất 1 mã (USD/market)
  // (Hook ở đây để không loop vô hạn: chỉ set lại nếu length === 0)
  if (selectedItems.length === 0) {
    setSelectedItems([{ code: "USD", type: "market" }]);
  }

  // Handler thêm mã mới vào so sánh
  function handleAddItem(item) {
    setSelectedItems((prev) => [...prev, item]);
  }
  // Handler xóa mã khỏi chart
  function handleRemoveItem(item) {
    setSelectedItems((prev) =>
      prev.filter((x) => x.code !== item.code || x.type !== item.type)
    );
  }
  // Chuyển mode default/normalize
  function handleSwitchMode() {
    setMode(mode === "default" ? "normalize" : "default");
  }

  // Xác định trạng thái loading thực tế của chart data
  const isChartLoading =
    loading.chart ||
    selectedItems.some(({ code, type }) => {
      const key = `${type}-${code}`;
      const arr = data[key]?.[days] || [];
      return arr.length === 0;
    });

  return (
    <div className="border rounded-md border-[#E7E7E7] shadow p-6 min-h-[586px]">
      <Tabs defaultValue={range} onValueChange={setRange} className="w-full">
        <div className="flex justify-between items-center mb-4">
          <RangeTabs ranges={ranges} value={range} onChange={setRange} />
          <div className="flex items-center gap-2">
            <AddItemDropdown
              options={exchangeOptions}
              selectedItems={selectedItems}
              getKey={(item) =>
                `${item.type || item.type}-${item.code || item.code}`
              }
              onSelect={handleAddItem}
              buttonLabel="Thêm mã so sánh"
              emptyText="Không còn mã nào"
            />

            <CompareSwitch
              checked={mode === "normalize"}
              onChange={handleSwitchMode}
            />
          </div>
        </div>
        <TabsContent value={range}>
          <div className="w-full transition-all">
            <ChartWrapper
              loading={isChartLoading}
              error={!!error.chart}
              loadingText="Đang tải dữ liệu..."
              errorText="Không thể kết nối đến máy chủ"
            >
              <ExchangeChart
                mode={mode}
                chartItems={selectedItems}
                setChartItems={setSelectedItems}
                data={data}
                range={range}
                options={exchangeOptions}
              />
            </ChartWrapper>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}
