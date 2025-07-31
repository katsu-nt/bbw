"use client";

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
import { fetchGoldChart } from "../../../store/market/goldSlice"; 
import GoldChart from "./GoldChart";
import { ResponseStatus } from "@/components/ui/responseStatus";

const simplifiedGoldOptions = [
  { code: "sjc", name: "SJC", location: "hcm" },
  { code: "pnj", name: "PNJ", location: "hcm" },
  { code: "vàng_nữ_trang_9999", name: "Nữ Trang 999.9", location: "tq" },
  { code: "vàng_nữ_trang_999", name: "Nữ Trang 999", location: "tq" },
  { code: "vàng_nữ_trang_9920", name: "Nữ Trang 9920", location: "tq" },
  { code: "vàng_nữ_trang_99", name: "Nữ Trang 99", location: "tq" },
  { code: "vàng_916_22k", name: "Vàng 916 (22K)", location: "tq" },
  { code: "vàng_750_18k", name: "Vàng 750 (18K)", location: "tq" },
  { code: "vàng_680_163k", name: "Vàng 680 (16.3K)", location: "tq" },
  { code: "vàng_650_156k", name: "Vàng 650 (15.6K)", location: "tq" },
  { code: "vàng_610_146k", name: "Vàng 610 (14.6K)", location: "tq" },
  { code: "vàng_585_14k", name: "Vàng 585 (14K)", location: "tq" },
  { code: "vàng_416_10k", name: "Vàng 416 (10K)", location: "tq" },
  { code: "vàng_375_9k", name: "Vàng 375 (9K)", location: "tq" },
  { code: "vàng_333_8k", name: "Vàng 333 (8K)", location: "tq" },
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

export default function GoldContainer() {
  const [selectedItems, setSelectedItems] = useState([
    { gold_type: "sjc", location: "hcm" },
  ]);
  const [range, setRange] = useState("7d");
  const dispatch = useDispatch();
  const { chart: data, loading, error } = useSelector((state) => state.gold); // ✅ Dùng `chart` từ slice gộp
  const days = getDaysFromRange(range);

  useEffect(() => {
    const needFetch = selectedItems.filter(({ gold_type, location }) => {
      const key = `${gold_type}-${location}`;
      return !data[key] || !data[key][days];
    });

    if (needFetch.length > 0) {
      const gold_types = needFetch.map((item) => item.gold_type);
      const locations = needFetch.map((item) => item.location);
      dispatch(fetchGoldChart({ gold_types, locations, days }));
    }
  }, [selectedItems, range, data, dispatch]);

  const filteredOptions = simplifiedGoldOptions.filter(
    (item) =>
      !selectedItems.some(
        (s) => s.gold_type === item.code && s.location === item.location
      )
  );

  return (
    <div className="border rounded-md border-[#E7E7E7] shadow p-6  min-h-[586px]">
      <Tabs defaultValue={range} onValueChange={setRange} className="w-full">
        <div className="flex justify-between items-center mb-4">
          <TabsList className="inline-flex h-[36px] bg-[#FAFAFA] rounded-lg shadow-[inset_0_0_0_1px_#E7E7E7] overflow-hidden">
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
          <DropdownMenu>
            <DropdownMenuTrigger asChild className="min-w-[280px] h-[36px]">
              <button className="relative flex items-center justify-between gap-2 px-4 py-2 border border-[#D5D7DA] rounded-lg shadow text-[#595959] text-base w-[220px] bg-white">
                <div className="flex items-center gap-2 text-base">
                  <Search className="w-4 h-4 text-[#A4A7AE]" />
                  Thêm so sánh
                </div>
                <ChevronDown className="w-4 h-4 text-[#BBBBBB]" />
              </button>
            </DropdownMenuTrigger>

            <DropdownMenuContent
              align="end"
              sideOffset={4}
              className="z-50 mt-1 max-h-[300px] overflow-y-auto w-[--radix-popper-anchor-width] rounded-md border border-[#E7E7E7] bg-white shadow"
            >
              {filteredOptions.map((item) => (
                <DropdownMenuItem
                  key={item.code}
                  onClick={() => {
                    setSelectedItems((prev) => [
                      ...prev,
                      { gold_type: item.code, location: item.location },
                    ]);
                  }}
                  className="text-sm px-3 py-2 cursor-pointer"
                >
                  {item.name}
                </DropdownMenuItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        <TabsContent value={range}>
          <div className="w-full transition-all">
            {loading.chart || error.chart ? ( 
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
                  {loading.chart ? (
                    <ResponseStatus
                      status="loading"
                      message="Dữ liệu đang được tải về..."
                    />
                  ) : (
                    <ResponseStatus status="error" />
                  )}
                </div>
              </div>
            ) : (
              <GoldChart
                data={data}
                selectedItems={selectedItems}
                setSelectedItems={setSelectedItems}
                range={range}
              />
            )}
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}
