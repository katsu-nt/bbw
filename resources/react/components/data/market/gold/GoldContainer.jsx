import { useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Tabs, TabsContent } from "@/components/ui/tabs";
import { fetchGoldChart } from "@/store/market/goldSlice";
import GoldChart from "./GoldChart";
import CompareSwitch from "@/components/data/market/shared/CompareSwitch";
import RangeTabs from "@/components/data/market/shared/RangeTabs";
import AddItemDropdown from "@/components/data/market/shared/AddItemDropdown";
import ChartWrapper from "@/components/data/market/shared/ChartWrapper";
import { getDaysFromRange } from "@/lib/market/date";
import { useChartData } from "@/hooks/useChartData";

const simplifiedGoldOptions = [
  { code: "sjc", name: "SJC", location: "hcm" },
  { code: "xau_usd", name: "Vàng thế giới", location: "global" },
  { code: "pnj", name: "PNJ", location: "hcm" },
  // ...add other types here if needed
];

const ranges = [
  { label: "1 Tuần", value: "7d" },
  { label: "1 Tháng", value: "30d" },
  { label: "6 Tháng", value: "6m" },
  { label: "YTD", value: "ytd" },
  { label: "1 Năm", value: "1y" },
  { label: "5 Năm", value: "5y" },
];

export default function GoldContainer() {
  const [mode, setMode] = useState("default");
  const [goldItems, setGoldItems] = useState([
    { gold_type: "sjc", location: "hcm" },
  ]);
  const [range, setRange] = useState("7d");
  const dispatch = useDispatch();
  const { chart: data, loading, error } = useSelector((state) => state.gold);

  const days = getDaysFromRange(range);

  // Custom hook dùng cho fetch chart data
  useChartData({
    items: goldItems,
    data,
    days,
    dispatch,
    fetchAction: fetchGoldChart,
    getKey: ({ gold_type, location }) => `${gold_type}-${location}`,
    getParams: (arr, days) => ({
      gold_types: arr.map((i) => i.gold_type),
      locations: arr.map((i) => i.location),
      days,
    }),
  });

  // Xác định trạng thái loading thực tế của chart data
  const isChartLoading =
    loading.chart ||
    goldItems.some(({ gold_type, location }) => {
      const key = `${gold_type}-${location}`;
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
              options={simplifiedGoldOptions}
              selectedItems={goldItems}
              getKey={(item) =>
                `${item.gold_type || item.code}-${item.location}`
              }
              onSelect={(item) =>
                setGoldItems((prev) => [
                  ...prev,
                  { gold_type: item.code, location: item.location },
                ])
              }
              buttonLabel="Thêm loại vàng"
              emptyText="Không còn loại nào để chọn"
            />

            <CompareSwitch
              checked={mode === "normalize"}
              onChange={() =>
                setMode(mode === "default" ? "normalize" : "default")
              }
              leftLabel="So sánh giá"
              rightLabel="Phần trăm (%)"
            />
          </div>
        </div>
        <TabsContent value={range}>
          <div className="w-full transition-all">
            <ChartWrapper
              loading={isChartLoading}
              error={!!error.chart}
              loadingText="Dữ liệu đang được tải về..."
              errorText="Không thể kết nối đến máy chủ"
            >
              <GoldChart
                mode={mode}
                goldItems={goldItems}
                setGoldItems={setGoldItems}
                data={data}
                range={range}
                options={simplifiedGoldOptions}
              />
            </ChartWrapper>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}
